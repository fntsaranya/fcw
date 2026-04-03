<?php
declare(strict_types=1);

use FCW\Support\Env;

const BASE_PATH = __DIR__ . '/..';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once BASE_PATH . '/src/Support/Env.php';
require_once BASE_PATH . '/src/Support/helpers.php';

Env::load(BASE_PATH . '/.env');

date_default_timezone_set((string) env('APP_TIMEZONE', 'Asia/Kolkata'));

$debug = filter_var((string) env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);

if ($debug) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
}

spl_autoload_register(
    static function (string $class): void {
        $prefix = 'FCW\\';

        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = BASE_PATH . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
);

$vendorAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($vendorAutoload)) {
    require_once $vendorAutoload;
}
