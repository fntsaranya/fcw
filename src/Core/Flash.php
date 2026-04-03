<?php
declare(strict_types=1);

namespace FCW\Core;

final class Flash
{
    private const KEY = '_flash_messages';

    public static function add(string $type, string $message): void
    {
        if (!isset($_SESSION[self::KEY]) || !is_array($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = [];
        }

        $_SESSION[self::KEY][] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /**
     * @return array<int, array{type:string,message:string}>
     */
    public static function pullAll(): array
    {
        $messages = $_SESSION[self::KEY] ?? [];
        unset($_SESSION[self::KEY]);

        return is_array($messages) ? $messages : [];
    }
}
