<?php
declare(strict_types=1);

namespace FCW\Controllers;

use FCW\Core\Database;
use FCW\Core\View;
use Throwable;

final class HealthController
{
    public function health(): void
    {
        View::json(['status' => 'ok']);
    }

    public function dbHealth(): void
    {
        try {
            $sql = Database::driver() === 'sqlite' ? 'SELECT sqlite_version()' : 'SELECT version()';
            $stmt = Database::connection()->query($sql);
            $version = (string) $stmt->fetchColumn();
            View::json([
                'status' => 'ok',
                'driver' => Database::driver(),
                'db_version' => $version,
            ]);
        } catch (Throwable $exception) {
            View::json(['status' => 'error', 'detail' => $exception->getMessage()], 500);
        }
    }
}
