<?php

declare(strict_types=1);

require_once __DIR__ . '/migrate.php';

if (filter_var(getenv('RUN_SEED') ?: 'true', FILTER_VALIDATE_BOOL)) {
    require __DIR__ . '/seed.php';
}

passthru('exec php -S 0.0.0.0:8000 -t public', $exitCode);
exit($exitCode);
