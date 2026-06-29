<?php
declare(strict_types=1);

namespace FCW\Repositories;

use FCW\Core\Database;
use PDO;
use PDOException;
use RuntimeException;

final class AppointmentRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(int $limit = 200): array
    {
        $sql = 'SELECT b.*,
                       o.gateway_order_id,
                       o.status AS gateway_order_status,
                       t.gateway_payment_id,
                       t.status AS gateway_payment_status,
                       t.method AS gateway_method,
                       t.checkout_signature_verified,
                       t.error_description AS gateway_error_description,
                       t.updated_at AS gateway_updated_at
                FROM appointment_bookings b
                LEFT JOIN appointment_payment_orders o
                  ON o.id = (
                      SELECT MAX(o2.id)
                      FROM appointment_payment_orders o2
                      WHERE o2.appointment_id = b.id
                  )
                LEFT JOIN appointment_payment_transactions t
                  ON t.id = (
                      SELECT MAX(t2.id)
                      FROM appointment_payment_transactions t2
                      WHERE t2.payment_order_id = o.id
                  )
                ORDER BY b.created_at DESC
                LIMIT :limit';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * @return array{id:int,booking_reference:string,ack_token:string}
     */
    public function create(
        string $fullName,
        string $email,
        string $phone,
        string $concern,
        ?string $preferredDate,
        ?string $preferredTime,
        string $amountInr,
        string $currency,
        ?string $sourceIp,
        ?string $userAgent
    ): array {
        $pdo = Database::connection();
        $maxAttempts = 5;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $reference = $this->generateBookingReference();
            $ackToken = bin2hex(random_bytes(32));

            try {
                if (Database::driver() === 'pgsql') {
                    $sql = 'INSERT INTO appointment_bookings (
                                booking_reference, ack_token, full_name, email, phone, concern,
                                preferred_date, preferred_time, amount_inr, currency, source_ip, user_agent
                            ) VALUES (
                                :booking_reference, :ack_token, :full_name, :email, :phone, :concern,
                                :preferred_date, :preferred_time, :amount_inr, :currency, :source_ip, :user_agent
                            ) RETURNING id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':booking_reference' => $reference,
                        ':ack_token' => $ackToken,
                        ':full_name' => $fullName,
                        ':email' => $email,
                        ':phone' => $phone,
                        ':concern' => $concern,
                        ':preferred_date' => $preferredDate,
                        ':preferred_time' => $preferredTime,
                        ':amount_inr' => $amountInr,
                        ':currency' => strtoupper($currency),
                        ':source_ip' => $sourceIp,
                        ':user_agent' => $userAgent,
                    ]);

                    return [
                        'id' => (int) $stmt->fetchColumn(),
                        'booking_reference' => $reference,
                        'ack_token' => $ackToken,
                    ];
                }

                $sql = 'INSERT INTO appointment_bookings (
                            booking_reference, ack_token, full_name, email, phone, concern,
                            preferred_date, preferred_time, amount_inr, currency, source_ip, user_agent
                        ) VALUES (
                            :booking_reference, :ack_token, :full_name, :email, :phone, :concern,
                            :preferred_date, :preferred_time, :amount_inr, :currency, :source_ip, :user_agent
                        )';
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':booking_reference' => $reference,
                    ':ack_token' => $ackToken,
                    ':full_name' => $fullName,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':concern' => $concern,
                    ':preferred_date' => $preferredDate,
                    ':preferred_time' => $preferredTime,
                    ':amount_inr' => $amountInr,
                    ':currency' => strtoupper($currency),
                    ':source_ip' => $sourceIp,
                    ':user_agent' => $userAgent,
                ]);

                return [
                    'id' => (int) $pdo->lastInsertId(),
                    'booking_reference' => $reference,
                    'ack_token' => $ackToken,
                ];
            } catch (PDOException $exception) {
                if ($this->isDuplicateKey($exception) && $attempt < $maxAttempts) {
                    continue;
                }

                throw $exception;
            }
        }

        throw new RuntimeException('Unable to generate unique booking reference.');
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByReference(string $reference): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM appointment_bookings WHERE booking_reference = :booking_reference LIMIT 1'
        );
        $stmt->execute([':booking_reference' => $reference]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function findByReferenceAndToken(string $reference, string $ackToken): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM appointment_bookings
             WHERE booking_reference = :booking_reference AND ack_token = :ack_token
             LIMIT 1'
        );
        $stmt->execute([
            ':booking_reference' => $reference,
            ':ack_token' => $ackToken,
        ]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function acknowledgePayment(
        string $reference,
        string $ackToken,
        ?string $upiTransactionRef,
        ?string $paymentChannel
    ): bool {
        $sql = 'UPDATE appointment_bookings
                SET payment_status = :payment_status,
                    upi_transaction_ref = :upi_transaction_ref,
                    payment_channel = :payment_channel,
                    payment_acknowledged_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP
                WHERE booking_reference = :booking_reference
                  AND ack_token = :ack_token';

        $stmt = Database::connection()->prepare($sql);

        return $stmt->execute([
            ':payment_status' => 'payment_submitted',
            ':upi_transaction_ref' => $upiTransactionRef,
            ':payment_channel' => $paymentChannel,
            ':booking_reference' => $reference,
            ':ack_token' => $ackToken,
        ]);
    }

    public function markVerified(int $id, ?string $note = null): bool
    {
        $sql = 'UPDATE appointment_bookings
                SET payment_status = :payment_status,
                    payment_verified_at = CURRENT_TIMESTAMP,
                    verification_note = :verification_note,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id';
        $stmt = Database::connection()->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':payment_status' => 'payment_verified',
            ':verification_note' => $note,
        ]);
    }

    public function markRejected(int $id, ?string $note = null): bool
    {
        $sql = 'UPDATE appointment_bookings
                SET payment_status = :payment_status,
                    payment_verified_at = NULL,
                    verification_note = :verification_note,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id';
        $stmt = Database::connection()->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':payment_status' => 'payment_rejected',
            ':verification_note' => $note,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM appointment_bookings WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    private function generateBookingReference(): string
    {
        return 'FCW' . date('ymdHis') . strtoupper(bin2hex(random_bytes(2)));
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
