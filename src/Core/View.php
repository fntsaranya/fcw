<?php
declare(strict_types=1);

namespace FCW\Core;

use Throwable;

final class View
{
    public static function render(string $template, array $data = [], int $statusCode = 200): void
    {
        http_response_code($statusCode);

        $contentTemplate = BASE_PATH . '/templates/' . $template . '.php';
        if (!is_file($contentTemplate)) {
            throw new \RuntimeException("Template not found: {$template}");
        }

        $flashMessages = Flash::pullAll();
        $contact = Config::contact();
        $appName = Config::appName();

        extract($data, EXTR_SKIP);

        include BASE_PATH . '/templates/layout.php';
    }

    public static function json(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public static function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }

    public static function renderDatabaseError(string $message, int $statusCode = 503): void
    {
        self::render('pages/error_db', ['errorMessage' => $message, 'activePage' => ''], $statusCode);
    }

    public static function tryOrDatabaseError(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (Throwable $exception) {
            error_log('Database operation failed: ' . $exception->getMessage());
            self::renderDatabaseError('Our database is currently unavailable. Please try again in a few minutes.');
            exit;
        }
    }
}
