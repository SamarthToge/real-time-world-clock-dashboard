<?php
/**
 * Simple info route so hitting the backend root doesn't 404.
 * The real endpoints live under /api/.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

send_json_headers();

echo json_encode([
    'name' => 'Meridian World Clock Console API',
    'endpoints' => [
        'GET/POST/PUT/DELETE' => '/api/alarms.php',
        'GET/POST' => '/api/timezones.php',
    ],
]);
