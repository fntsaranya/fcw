<?php
declare(strict_types=1);

namespace FCW\Repositories;

use FCW\Core\Database;
use PDO;
use PDOException;
use RuntimeException;
use Throwable;

final class AppointmentPaymentRepository
{
    /**
     * @return array<string, mixed>|null
     */
    public function latestOrderForAppointment(int $appointmentId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM appointment_payment_orders
             WHERE appointment_id = :appointment_id
             ORDER BY id DESC
             LIMIT 1'
        );
        $stmt->execute([':appointment_id' => $appointmentId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findOrderByGatewayId(string $gatewayOrderId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM appointment_payment_orders
             WHERE gateway_order_id = :gateway_order_id
             LIMIT 1'
        );
        $stmt->execute([':gateway_order_id' => $gatewayOrderId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @param array<string, mixed> $gatewayOrder
     * @return array<string, mixed>
     */
    public function createOrder(int $appointmentId, array $gatewayOrder): array
    {
        $gatewayOrderId = trim((string) ($gatewayOrder['id'] ?? ''));
        $amount = (int) ($gatewayOrder['amount'] ?? 0);
        $currency = strtoupper(trim((string) ($gatewayOrder['currency'] ?? '')));

        if ($gatewayOrderId === '' || $amount < 1 || $currency === '') {
            throw new RuntimeException('Razorpay returned incomplete order details.');
        }

        $pdo = Database::connection();
        $sql = 'INSERT INTO appointment_payment_orders (
                    appointment_id, gateway, gateway_order_id, amount_subunits,
                    currency, status, gateway_created_at
                ) VALUES (
                    :appointment_id, :gateway, :gateway_order_id, :amount_subunits,
                    :currency, :status, :gateway_created_at
                )';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':appointment_id' => $appointmentId,
            ':gateway' => 'razorpay',
            ':gateway_order_id' => $gatewayOrderId,
            ':amount_subunits' => $amount,
            ':currency' => $currency,
            ':status' => (string) ($gatewayOrder['status'] ?? 'created'),
            ':gateway_created_at' => $this->timestampFromUnix($gatewayOrder['created_at'] ?? null),
        ]);

        $id = (int) $pdo->lastInsertId();
        if (Database::driver() === 'pgsql') {
            $id = (int) $pdo->query(
                "SELECT currval(pg_get_serial_sequence('appointment_payment_orders', 'id'))"
            )->fetchColumn();
        }

        return [
            'id' => $id,
            'appointment_id' => $appointmentId,
            'gateway' => 'razorpay',
            'gateway_order_id' => $gatewayOrderId,
            'amount_subunits' => $amount,
            'currency' => $currency,
            'status' => (string) ($gatewayOrder['status'] ?? 'created'),
        ];
    }

    /**
     * @param array<string, mixed> $payment
     * @return array{booking_status:string,gateway_status:string}
     */
    public function recordPayment(array $payment, bool $checkoutSignatureVerified, string $source): array
    {
        $gatewayOrderId = trim((string) ($payment['order_id'] ?? ''));
        $gatewayPaymentId = trim((string) ($payment['id'] ?? ''));
        $gatewayStatus = strtolower(trim((string) ($payment['status'] ?? '')));

        if ($gatewayOrderId === '' || $gatewayPaymentId === '' || $gatewayStatus === '') {
            throw new RuntimeException('Razorpay returned incomplete payment details.');
        }

        $order = $this->findOrderByGatewayId($gatewayOrderId);
        if ($order === null) {
            throw new RuntimeException('No local booking order matches this Razorpay payment.');
        }

        $amount = (int) ($payment['amount'] ?? 0);
        $currency = strtoupper(trim((string) ($payment['currency'] ?? '')));
        if ($amount !== (int) $order['amount_subunits'] || $currency !== (string) $order['currency']) {
            throw new RuntimeException('Razorpay payment amount or currency does not match the booking.');
        }

        $refundStatus = strtolower(trim((string) ($payment['refund_status'] ?? '')));
        if ($refundStatus === 'full' || $gatewayStatus === 'refunded') {
            $gatewayStatus = 'refunded';
        } elseif ($refundStatus === 'partial') {
            $gatewayStatus = 'partially_refunded';
        }

        $pdo = Database::connection();
        $pdo->beginTransaction();

        try {
            $existing = $this->findTransactionByGatewayId($gatewayPaymentId);
            $storedStatus = $existing === null
                ? $gatewayStatus
                : $this->nonRegressiveStatus((string) $existing['status'], $gatewayStatus);
            $signatureVerified = $checkoutSignatureVerified
                || (bool) ((int) ($existing['checkout_signature_verified'] ?? 0));

            if ($existing === null) {
                $stmt = $pdo->prepare(
                    'INSERT INTO appointment_payment_transactions (
                        payment_order_id, gateway_payment_id, amount_subunits, currency,
                        status, method, checkout_signature_verified, record_source,
                        amount_refunded_subunits, refund_status, error_code, error_description,
                        error_source, error_step, error_reason, gateway_created_at,
                        paid_at, failed_at
                    ) VALUES (
                        :payment_order_id, :gateway_payment_id, :amount_subunits, :currency,
                        :status, :method, :checkout_signature_verified, :record_source,
                        :amount_refunded_subunits, :refund_status, :error_code, :error_description,
                        :error_source, :error_step, :error_reason, :gateway_created_at,
                        :paid_at, :failed_at
                    )'
                );
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE appointment_payment_transactions
                     SET status = :status,
                         method = :method,
                         checkout_signature_verified = :checkout_signature_verified,
                         record_source = :record_source,
                         amount_refunded_subunits = :amount_refunded_subunits,
                         refund_status = :refund_status,
                         error_code = :error_code,
                         error_description = :error_description,
                         error_source = :error_source,
                         error_step = :error_step,
                         error_reason = :error_reason,
                         paid_at = COALESCE(paid_at, :paid_at),
                         failed_at = COALESCE(failed_at, :failed_at),
                         updated_at = CURRENT_TIMESTAMP
                     WHERE gateway_payment_id = :gateway_payment_id'
                );
            }

            $parameters = [
                ':gateway_payment_id' => $gatewayPaymentId,
                ':status' => $storedStatus,
                ':method' => $this->nullableString($payment['method'] ?? null),
                ':checkout_signature_verified' => $signatureVerified,
                ':record_source' => substr($source, 0, 30),
                ':amount_refunded_subunits' => max(0, (int) ($payment['amount_refunded'] ?? 0)),
                ':refund_status' => $this->nullableString($payment['refund_status'] ?? null),
                ':error_code' => $this->nullableString($payment['error_code'] ?? null),
                ':error_description' => $this->nullableString($payment['error_description'] ?? null),
                ':error_source' => $this->nullableString($payment['error_source'] ?? null),
                ':error_step' => $this->nullableString($payment['error_step'] ?? null),
                ':error_reason' => $this->nullableString($payment['error_reason'] ?? null),
                ':paid_at' => in_array($storedStatus, ['captured', 'partially_refunded', 'refunded'], true)
                    ? date('Y-m-d H:i:s')
                    : null,
                ':failed_at' => $storedStatus === 'failed' ? date('Y-m-d H:i:s') : null,
            ];

            if ($existing === null) {
                $parameters += [
                    ':payment_order_id' => (int) $order['id'],
                    ':amount_subunits' => $amount,
                    ':currency' => $currency,
                    ':gateway_created_at' => $this->timestampFromUnix($payment['created_at'] ?? null),
                ];
            }

            $stmt->execute($parameters);

            $orderStatus = match ($storedStatus) {
                'captured', 'partially_refunded', 'refunded' => 'paid',
                default => 'attempted',
            };
            $orderStmt = $pdo->prepare(
                'UPDATE appointment_payment_orders
                 SET status = CASE WHEN status = :paid THEN status ELSE :status END,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id'
            );
            $orderStmt->execute([
                ':paid' => 'paid',
                ':status' => $orderStatus,
                ':id' => (int) $order['id'],
            ]);

            $bookingStatus = $this->syncBookingStatus(
                (int) $order['appointment_id'],
                $gatewayPaymentId,
                $this->nullableString($payment['method'] ?? null)
            );

            $pdo->commit();

            return [
                'booking_status' => $bookingStatus,
                'gateway_status' => $storedStatus,
            ];
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function beginWebhookEvent(
        string $eventId,
        string $eventType,
        ?string $gatewayOrderId,
        ?string $gatewayPaymentId,
        string $payloadHash
    ): bool {
        $pdo = Database::connection();
        $existing = $pdo->prepare(
            'SELECT processing_status FROM razorpay_webhook_events WHERE event_id = :event_id LIMIT 1'
        );
        $existing->execute([':event_id' => $eventId]);
        $status = $existing->fetchColumn();

        if ($status === 'processed') {
            return false;
        }

        if ($status !== false) {
            $retry = $pdo->prepare(
                'UPDATE razorpay_webhook_events
                 SET processing_status = :processing_status,
                     last_error = NULL,
                     updated_at = CURRENT_TIMESTAMP
                 WHERE event_id = :event_id'
            );
            $retry->execute([
                ':processing_status' => 'processing',
                ':event_id' => $eventId,
            ]);
            return true;
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO razorpay_webhook_events (
                    event_id, event_type, gateway_order_id, gateway_payment_id,
                    payload_sha256, processing_status
                ) VALUES (
                    :event_id, :event_type, :gateway_order_id, :gateway_payment_id,
                    :payload_sha256, :processing_status
                )'
            );
            $stmt->execute([
                ':event_id' => $eventId,
                ':event_type' => $eventType,
                ':gateway_order_id' => $gatewayOrderId,
                ':gateway_payment_id' => $gatewayPaymentId,
                ':payload_sha256' => $payloadHash,
                ':processing_status' => 'processing',
            ]);
            return true;
        } catch (PDOException $exception) {
            if ($this->isDuplicateKey($exception)) {
                return false;
            }
            throw $exception;
        }
    }

    public function completeWebhookEvent(string $eventId): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE razorpay_webhook_events
             SET processing_status = :processing_status,
                 processed_at = CURRENT_TIMESTAMP,
                 last_error = NULL,
                 updated_at = CURRENT_TIMESTAMP
             WHERE event_id = :event_id'
        );
        $stmt->execute([
            ':processing_status' => 'processed',
            ':event_id' => $eventId,
        ]);
    }

    public function failWebhookEvent(string $eventId, string $message): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE razorpay_webhook_events
             SET processing_status = :processing_status,
                 last_error = :last_error,
                 updated_at = CURRENT_TIMESTAMP
             WHERE event_id = :event_id'
        );
        $stmt->execute([
            ':processing_status' => 'failed',
            ':last_error' => substr($message, 0, 1000),
            ':event_id' => $eventId,
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function latestTransactionForAppointment(int $appointmentId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT t.*, o.gateway_order_id
             FROM appointment_payment_transactions t
             INNER JOIN appointment_payment_orders o ON o.id = t.payment_order_id
             WHERE o.appointment_id = :appointment_id
             ORDER BY t.id DESC
             LIMIT 1'
        );
        $stmt->execute([':appointment_id' => $appointmentId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findTransactionByGatewayId(string $gatewayPaymentId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM appointment_payment_transactions
             WHERE gateway_payment_id = :gateway_payment_id
             LIMIT 1'
        );
        $stmt->execute([':gateway_payment_id' => $gatewayPaymentId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    private function syncBookingStatus(int $appointmentId, string $gatewayPaymentId, ?string $method): string
    {
        $stmt = Database::connection()->prepare(
            'SELECT t.status
             FROM appointment_payment_transactions t
             INNER JOIN appointment_payment_orders o ON o.id = t.payment_order_id
             WHERE o.appointment_id = :appointment_id'
        );
        $stmt->execute([':appointment_id' => $appointmentId]);
        $statuses = array_map('strval', $stmt->fetchAll(PDO::FETCH_COLUMN));

        $bookingStatus = 'payment_failed';
        if (in_array('refunded', $statuses, true)) {
            $bookingStatus = 'payment_refunded';
        } elseif (in_array('partially_refunded', $statuses, true)) {
            $bookingStatus = 'payment_partially_refunded';
        } elseif (in_array('captured', $statuses, true)) {
            $bookingStatus = 'payment_verified';
        } elseif (in_array('authorized', $statuses, true)) {
            $bookingStatus = 'payment_authorized';
        } elseif (in_array('created', $statuses, true)) {
            $bookingStatus = 'payment_initiated';
        }

        $verifiedAtSql = in_array($bookingStatus, ['payment_verified', 'payment_partially_refunded', 'payment_refunded'], true)
            ? 'COALESCE(payment_verified_at, CURRENT_TIMESTAMP)'
            : 'payment_verified_at';
        $sql = "UPDATE appointment_bookings
                SET payment_status = :payment_status,
                    upi_transaction_ref = :gateway_payment_id,
                    payment_channel = :payment_channel,
                    payment_acknowledged_at = COALESCE(payment_acknowledged_at, CURRENT_TIMESTAMP),
                    payment_verified_at = {$verifiedAtSql},
                    verification_note = :verification_note,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        $update = Database::connection()->prepare($sql);
        $update->execute([
            ':payment_status' => $bookingStatus,
            ':gateway_payment_id' => $gatewayPaymentId,
            ':payment_channel' => $method === null ? 'razorpay' : 'razorpay_' . $method,
            ':verification_note' => 'Razorpay status synchronized: ' . $bookingStatus,
            ':id' => $appointmentId,
        ]);

        return $bookingStatus;
    }

    private function nonRegressiveStatus(string $current, string $incoming): string
    {
        $rank = [
            'created' => 0,
            'failed' => 1,
            'authorized' => 2,
            'captured' => 3,
            'partially_refunded' => 4,
            'refunded' => 5,
        ];

        return ($rank[$incoming] ?? -1) >= ($rank[$current] ?? -1) ? $incoming : $current;
    }

    private function nullableString(mixed $value): ?string
    {
        $normalized = trim((string) ($value ?? ''));
        return $normalized === '' ? null : $normalized;
    }

    private function timestampFromUnix(mixed $value): ?string
    {
        $timestamp = (int) ($value ?? 0);
        return $timestamp > 0 ? date('Y-m-d H:i:s', $timestamp) : null;
    }

    private function isDuplicateKey(PDOException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        if ($sqlState === '23505') {
            return true;
        }

        return $sqlState === '23000' && in_array($driverCode, [19, 1062], true);
    }
}
