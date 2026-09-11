<?php

declare(strict_types=1);

namespace App\Security;

use RuntimeException;

final class CsrfToken
{
    private const SESSION_KEY = '_csrf_token';

    public function get(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException('La session doit être démarrée avant l’utilisation du token CSRF.');
        }

        if (!isset($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public function validate(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE || !is_string($token)) {
            return false;
        }

        $expected = $_SESSION[self::SESSION_KEY] ?? null;

        return is_string($expected) && hash_equals($expected, $token);
    }
}
