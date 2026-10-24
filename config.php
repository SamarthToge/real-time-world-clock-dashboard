<?php
/**
 * Shared configuration and lightweight JSON-file "storage" helpers.
 * No database required — good for quick GitHub demos / small deployments.
 * Swap read_store()/write_store() for a PDO layer if you need a real DB.
 */

declare(strict_types=1);

define('DATA_DIR', __DIR__ . '/data');

if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0775, true);
}

/** Sends standard JSON + CORS headers for every API response. */
function send_json_headers(): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/** Reads and JSON-decodes a data file, returning $default if missing/empty. */
function read_store(string $filename, $default = [])
{
    $path = DATA_DIR . '/' . $filename;
    if (!file_exists($path)) {
        return $default;
    }

    $handle = fopen($path, 'r');
    if (!$handle) {
        return $default;
    }

    flock($handle, LOCK_SH);
    $raw = stream_get_contents($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    if ($raw === '' || $raw === false) {
        return $default;
    }

    $decoded = json_decode($raw, true);
    return $decoded ?? $default;
}

/** JSON-encodes and atomically writes a data file with an exclusive lock. */
function write_store(string $filename, $data): void
{
    $path = DATA_DIR . '/' . $filename;
    $handle = fopen($path, 'c+');
    if (!$handle) {
        throw new RuntimeException("Unable to open $filename for writing");
    }

    flock($handle, LOCK_EX);
    ftruncate($handle, 0);
    rewind($handle);
    fwrite($handle, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);
}

/** Reads and JSON-decodes the raw request body. */
function read_json_body(): array
{
    $raw = file_get_contents('php://input');
    if (!$raw) {
        return [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

/** Sends a JSON error response and stops execution. */
function json_error(string $message, int $status = 400): void
{
    http_response_code($status);
    echo json_encode(['error' => $message]);
    exit;
}
