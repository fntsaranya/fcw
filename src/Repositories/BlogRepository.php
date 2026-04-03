<?php
declare(strict_types=1);

namespace FCW\Repositories;

use FCW\Core\Database;
use PDO;

final class BlogRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(int $limit = 100): array
    {
        $sql = 'SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT :limit';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM blog_posts WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    public function create(string $title, string $content, ?string $imageUrl): int
    {
        $pdo = Database::connection();

        if (Database::driver() === 'pgsql') {
            $stmt = $pdo->prepare(
                'INSERT INTO blog_posts (title, content, image_url) VALUES (:title, :content, :image_url) RETURNING id'
            );

            $stmt->execute([
                ':title' => $title,
                ':content' => $content,
                ':image_url' => $imageUrl,
            ]);

            return (int) $stmt->fetchColumn();
        }

        $stmt = $pdo->prepare(
            'INSERT INTO blog_posts (title, content, image_url) VALUES (:title, :content, :image_url)'
        );

        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':image_url' => $imageUrl,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function update(int $id, string $title, string $content, ?string $imageUrl): bool
    {
        $stmt = Database::connection()->prepare(
            'UPDATE blog_posts
             SET title = :title, content = :content, image_url = :image_url
             WHERE id = :id'
        );

        return $stmt->execute([
            ':id' => $id,
            ':title' => $title,
            ':content' => $content,
            ':image_url' => $imageUrl,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::connection()->prepare('DELETE FROM blog_posts WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
