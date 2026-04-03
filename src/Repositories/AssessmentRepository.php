<?php
declare(strict_types=1);

namespace FCW\Repositories;

use FCW\Core\Database;
use FCW\Services\AssessmentCatalog;
use PDO;

final class AssessmentRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(int $limit = 100): array
    {
        $sql = 'SELECT * FROM health_assessments ORDER BY created_at DESC LIMIT :limit';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM health_assessments WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /**
     * @param array<string, bool> $fieldValues
     */
    public function create(
        string $fullName,
        string $email,
        string $phone,
        int $score,
        string $interpretation,
        array $fieldValues
    ): int {
        $columns = ['full_name', 'email', 'phone', 'score', 'interpretation'];
        $placeholders = [':full_name', ':email', ':phone', ':score', ':interpretation'];
        $params = [
            ':full_name' => $fullName,
            ':email' => $email,
            ':phone' => $phone,
            ':score' => $score,
            ':interpretation' => $interpretation,
        ];

        foreach (AssessmentCatalog::booleanFields() as $fieldName) {
            $columns[] = $fieldName;
            $placeholders[] = ':' . $fieldName;
            $params[':' . $fieldName] = !empty($fieldValues[$fieldName]);
        }

        $pdo = Database::connection();

        if (Database::driver() === 'pgsql') {
            $sql = sprintf(
                'INSERT INTO health_assessments (%s) VALUES (%s) RETURNING id',
                implode(', ', $columns),
                implode(', ', $placeholders)
            );

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            return (int) $stmt->fetchColumn();
        }

        $sql = sprintf(
            'INSERT INTO health_assessments (%s) VALUES (%s)',
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $pdo->lastInsertId();
    }

    /**
     * @param array<string, bool> $fieldValues
     */
    public function update(
        int $id,
        string $fullName,
        string $email,
        string $phone,
        int $score,
        string $interpretation,
        array $fieldValues
    ): bool {
        $setParts = [
            'full_name = :full_name',
            'email = :email',
            'phone = :phone',
            'score = :score',
            'interpretation = :interpretation',
        ];

        $params = [
            ':id' => $id,
            ':full_name' => $fullName,
            ':email' => $email,
            ':phone' => $phone,
            ':score' => $score,
            ':interpretation' => $interpretation,
        ];

        foreach (AssessmentCatalog::booleanFields() as $fieldName) {
            $setParts[] = "{$fieldName} = :{$fieldName}";
            $params[':' . $fieldName] = !empty($fieldValues[$fieldName]);
        }

        $sql = sprintf('UPDATE health_assessments SET %s WHERE id = :id', implode(', ', $setParts));
        $stmt = Database::connection()->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM health_assessments WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
