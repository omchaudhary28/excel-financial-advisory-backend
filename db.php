<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/cors.php';

try {
    \ = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;sslmode=require",
        \['DB_HOST'],
        \['DB_PORT'],
        \['DB_NAME']
    );

    \ = new PDO(\, \['DB_USER'], \['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

} catch (PDOException \) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed'
    ]);
    exit;
}
