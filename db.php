<?php
require_once __DIR__ . "/cors.php";

try {
    $host = getenv("DB_HOST");
    $port = getenv("DB_PORT");
    $db   = getenv("DB_NAME");
    $user = getenv("DB_USER");
    $pass = getenv("DB_PASSWORD");

    $dsn = "pgsql:host=$host;port=$port;dbname=$db";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
    exit;
}
