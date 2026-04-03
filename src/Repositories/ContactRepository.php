<?php
declare(strict_types=1);

namespace FCW\Repositories;

use FCW\Core\Database;
use PDO;

final class ContactRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(int $limit = 100): array
    {
        $sql = 'SELECT * FROM contact_submissions ORDER BY created_at DESC LIMIT :limit';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create(string $name, string $email, string $phone, string $message): int
    {
        $pdo = Database::connection();

        if (Database::driver() === 'pgsql') {
            $sql = 'INSERT INTO contact_submissions (name, email, phone, message) VALUES (:name, :email, :phone, :message) RETURNING id';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':message' => $message,
            ]);

            return (int) $stmt->fetchColumn();
        }

        $sql = 'INSERT INTO contact_submissions (name, email, phone, message) VALUES (:name, :email, :phone, :message)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':message' => $message,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function update(int $id, string $name, string $email, string $phone, string $message): bool
    {
        $sql = 'UPDATE contact_submissions
                SET name = :name, email = :email, phone = :phone, message = :message
                WHERE id = :id';
        $stmt = Database::connection()->prepare($sql);

        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':message' => $message,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM contact_submissions WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
