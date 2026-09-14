<?php
declare(strict_types=1);

namespace FCW\Repositories;

use FCW\Core\Database;
use PDO;
use RuntimeException;
use Throwable;

final class EnquiryRegistrationRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(int $limit = 5000): array
    {
        try {
            $sql = 'SELECT r.*,
                           r.gateway_order_id AS latest_order_id,
                           r.gateway_payment_id AS latest_payment_id,
                           r.payment_method AS payment_method_used,
                           r.failure_reason AS latest_error
                    FROM enquiry_registrations r
                    ORDER BY r.created_at DESC, r.id DESC
                    LIMIT :limit';
            $stmt = Database::connection()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            error_log('EnquiryRegistrationRepository all() primary query error: ' . $e->getMessage());
            try {
                $stmt = Database::connection()->query('SELECT * FROM enquiry_registrations ORDER BY id DESC LIMIT 5000');
                return $stmt ? ($stmt->fetchAll(PDO::FETCH_ASSOC) ?: []) : [];
            } catch (Throwable $e2) {
                error_log('EnquiryRegistrationRepository all() fallback query error: ' . $e2->getMessage());
                return [];
            }
        }
    }

    /**
     * @param array<string, mixed> $webinar
     * @return array{id:int,registration_reference:string,ack_token:string,amount_inr:float}
     */
    public function createRegistration(
        string $name,
        string $email,
        string $phone,
        ?string $message,
        array $webinar,
        float $amountInr,
        ?string $sourceIp = null,
        ?string $userAgent = null
    ): array {
        $pdo = Database::connection();
        $reference = 'WEB-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $ackToken = bin2hex(random_bytes(24));
        $webinarId = !empty($webinar['id']) ? (int) $webinar['id'] : null;
        $webinarTitle = (string) ($webinar['title'] ?? 'Webinar / Enquiry');
        $initialStatus = $amountInr <= 0.00 ? 'free_registered' : 'pending_payment';
        $regStatus = $amountInr <= 0.00 ? 'confirmed' : 'pending';

        $sql = 'INSERT INTO enquiry_registrations (
                    registration_reference, ack_token, webinar_id, webinar_title,
                    name, email, phone, message, amount_inr, currency,
                    payment_status, registration_status, source_ip, user_agent
                ) VALUES (
                    :registration_reference, :ack_token, :webinar_id, :webinar_title,
                    :name, :email, :phone, :message, :amount_inr, :currency,
                    :payment_status, :registration_status, :source_ip, :user_agent
                )';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':registration_reference' => $reference,
            ':ack_token' => $ackToken,
            ':webinar_id' => $webinarId,
            ':webinar_title' => $webinarTitle,
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':message' => $message,
            ':amount_inr' => $amountInr,
            ':currency' => 'INR',
            ':payment_status' => $initialStatus,
            ':registration_status' => $regStatus,
            ':source_ip' => $sourceIp,
            ':user_agent' => $userAgent ? substr($userAgent, 0, 500) : null,
        ]);

        $id = (int) $pdo->lastInsertId();

        return [
            'id' => $id,
            'registration_reference' => $reference,
            'ack_token' => $ackToken,
            'amount_inr' => $amountInr,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByReference(string $reference): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM enquiry_registrations WHERE registration_reference = :reference LIMIT 1'
        );
        $stmt->execute([':reference' => $reference]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByReferenceAndToken(string $reference, string $ackToken): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM enquiry_registrations
             WHERE registration_reference = :reference AND ack_token = :ack_token
             LIMIT 1'
        );
        $stmt->execute([
            ':reference' => $reference,
            ':ack_token' => $ackToken,
        ]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @param array<string, mixed> $gatewayOrder
     * @return array<string, mixed>
     */
    public function createOrder(int $registrationId, array $gatewayOrder): array
    {
        $gatewayOrderId = trim((string) ($gatewayOrder['id'] ?? ''));
        $amount = (int) ($gatewayOrder['amount'] ?? 0);
        $currency = strtoupper(trim((string) ($gatewayOrder['currency'] ?? 'INR')));

        if ($gatewayOrderId === '' || $amount < 1) {
            throw new RuntimeException('Razorpay returned incomplete order details.');
        }

        $pdo = Database::connection();
        $sql = 'INSERT INTO enquiry_payment_orders (
                    registration_id, gateway, gateway_order_id, amount_subunits,
                    currency, status, gateway_created_at
                ) VALUES (
                    :registration_id, :gateway, :gateway_order_id, :amount_subunits,
                    :currency, :status, :gateway_created_at
                )';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':registration_id' => $registrationId,
            ':gateway' => 'razorpay',
            ':gateway_order_id' => $gatewayOrderId,
            ':amount_subunits' => $amount,
            ':currency' => $currency,
            ':status' => (string) ($gatewayOrder['status'] ?? 'created'),
            ':gateway_created_at' => !empty($gatewayOrder['created_at']) ? date('Y-m-d H:i:s', (int)$gatewayOrder['created_at']) : null,
        ]);

        $id = (int) $pdo->lastInsertId();

        return [
            'id' => $id,
            'registration_id' => $registrationId,
            'gateway_order_id' => $gatewayOrderId,
            'amount_subunits' => $amount,
            'currency' => $currency,
            'status' => 'created',
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findOrderByGatewayId(string $gatewayOrderId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM enquiry_payment_orders WHERE gateway_order_id = :gateway_order_id LIMIT 1'
        );
        $stmt->execute([':gateway_order_id' => $gatewayOrderId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function latestOrderForRegistration(int $registrationId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM enquiry_payment_orders WHERE registration_id = :registration_id ORDER BY id DESC LIMIT 1'
        );
        $stmt->execute([':registration_id' => $registrationId]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function recordTransaction(
        int $orderId,
        string $paymentId,
        int $amountSubunits,
        string $currency,
        string $status,
        ?string $method,
        bool $signatureVerified,
        ?string $errorCode = null,
        ?string $errorDesc = null
    ): int {
        $pdo = Database::connection();
        $now = Database::driver() === 'pgsql' ? 'NOW()' : (Database::driver() === 'mysql' ? 'NOW()' : 'CURRENT_TIMESTAMP');
        $paidAt = in_array($status, ['captured', 'authorized'], true) ? date('Y-m-d H:i:s') : null;
        $failedAt = $status === 'failed' ? date('Y-m-d H:i:s') : null;

        $sql = 'INSERT INTO enquiry_payment_transactions (
                    payment_order_id, gateway_payment_id, amount_subunits,
                    currency, status, method, checkout_signature_verified,
                    error_code, error_description, paid_at, failed_at
                ) VALUES (
                    :payment_order_id, :gateway_payment_id, :amount_subunits,
                    :currency, :status, :method, :signature_verified,
                    :error_code, :error_description, :paid_at, :failed_at
                )';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':payment_order_id' => $orderId,
            ':gateway_payment_id' => $paymentId,
            ':amount_subunits' => $amountSubunits,
            ':currency' => $currency,
            ':status' => $status,
            ':method' => $method,
            ':signature_verified' => $signatureVerified ? 1 : 0,
            ':error_code' => $errorCode,
            ':error_description' => $errorDesc,
            ':paid_at' => $paidAt,
            ':failed_at' => $failedAt,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function markPaymentSuccess(int $registrationId, string $orderId, string $paymentId, ?string $method = null): bool
    {
        $pdo = Database::connection();
        $now = Database::driver() === 'pgsql' ? 'NOW()' : (Database::driver() === 'mysql' ? 'NOW()' : 'CURRENT_TIMESTAMP');

        $sql = 'UPDATE enquiry_registrations SET
                    payment_status = \'payment_verified\',
                    registration_status = \'confirmed\',
                    gateway_order_id = :order_id,
                    gateway_payment_id = :payment_id,
                    payment_method = :method,
                    paid_at = ' . $now . ',
                    updated_at = ' . $now . '
                WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':order_id' => $orderId,
            ':payment_id' => $paymentId,
            ':method' => $method,
            ':id' => $registrationId,
        ]);
    }

    public function markPaymentFailed(int $registrationId, string $orderId, ?string $paymentId = null, ?string $failureReason = null): bool
    {
        $pdo = Database::connection();
        $now = Database::driver() === 'pgsql' ? 'NOW()' : (Database::driver() === 'mysql' ? 'NOW()' : 'CURRENT_TIMESTAMP');

        $sql = 'UPDATE enquiry_registrations SET
                    payment_status = \'payment_failed\',
                    gateway_order_id = :order_id,
                    gateway_payment_id = :payment_id,
                    failed_at = ' . $now . ',
                    failure_reason = :failure_reason,
                    updated_at = ' . $now . '
                WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':order_id' => $orderId,
            ':payment_id' => $paymentId,
            ':failure_reason' => $failureReason,
            ':id' => $registrationId,
        ]);
    }

    public function markWhatsAppRedirected(int $registrationId): bool
    {
        $pdo = Database::connection();
        $now = Database::driver() === 'pgsql' ? 'NOW()' : (Database::driver() === 'mysql' ? 'NOW()' : 'CURRENT_TIMESTAMP');

        $sql = 'UPDATE enquiry_registrations SET
                    whatsapp_redirected = 1,
                    whatsapp_redirected_at = ' . $now . ',
                    updated_at = ' . $now . '
                WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':id' => $registrationId]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM enquiry_registrations WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
