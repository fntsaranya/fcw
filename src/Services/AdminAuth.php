<?php
declare(strict_types=1);

namespace FCW\Services;

use FCW\Core\Config;

final class AdminAuth
{
    public static function verifyPin(string $pin): bool
    {
        $expectedPin = Config::adminPin();

        if ($pin === '' || $expectedPin === '') {
            return false;
        }

        return hash_equals($expectedPin, $pin);
    }
}
