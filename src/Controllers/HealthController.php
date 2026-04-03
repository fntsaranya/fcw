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
            $stmt = Database::connection()->query('SELECT version()');
            $version = (string) $stmt->fetchColumn();
            View::json(['status' => 'ok', 'db_version' => $version]);
        } catch (Throwable $exception) {
            View::json(['status' => 'error', 'detail' => $exception->getMessage()], 500);
        }
    }
}
