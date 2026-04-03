<?php
declare(strict_types=1);

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        return $value;
    }
}

if (!function_exists('env_bool')) {
    function env_bool(string $key, bool $default = false): bool
    {
        $raw = env($key, null);
        if ($raw === null) {
            return $default;
        }

        return filter_var((string) $raw, FILTER_VALIDATE_BOOLEAN);
    }
}

if (!function_exists('env_int')) {
    function env_int(string $key, int $default = 0): int
    {
        $raw = env($key, null);
        if ($raw === null) {
            return $default;
        }

        return (int) $raw;
    }
}

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/static/' . ltrim($path, '/');
    }
}

if (!function_exists('str_limit')) {
    function str_limit(string $value, int $limit = 150, string $suffix = '...'): string
    {
        if ($limit < 0) {
            return $value;
        }

        if (function_exists('mb_strlen') && function_exists('mb_substr')) {
            if (mb_strlen($value, 'UTF-8') <= $limit) {
                return $value;
            }

            return mb_substr($value, 0, $limit, 'UTF-8') . $suffix;
        }

        if (strlen($value) <= $limit) {
            return $value;
        }

        return substr($value, 0, $limit) . $suffix;
    }
}
