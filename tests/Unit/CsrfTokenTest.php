<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Security\CsrfToken;
use PHPUnit\Framework\TestCase;

final class CsrfTokenTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        session_id('csrf-test-' . bin2hex(random_bytes(4)));
        session_start();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
    }

    public function testGenereUnTokenEtLeValide(): void
    {
        $csrf = new CsrfToken();

        $token = $csrf->get();

        self::assertNotSame('', $token);
        self::assertTrue($csrf->validate($token));
    }

    public function testRefuseUnTokenInvalide(): void
    {
        $csrf = new CsrfToken();
        $csrf->get();

        self::assertFalse($csrf->validate('token-invalide'));
    }

    public function testConserveLeMemeTokenPendantLaSession(): void
    {
        $csrf = new CsrfToken();

        self::assertSame($csrf->get(), $csrf->get());
    }
}
