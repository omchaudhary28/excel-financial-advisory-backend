<?php
require_once 'cors.php';
require_once 'db_connect.php';
require_once 'middleware_auth.php';

// Admin authentication (JWT)
$user = authenticate(true);

// Default response (frontend-safe)
$response = [
    "total_users" => 0,
    "total_queries" => 0,
    "total_ratings" => 0
];

try {
    // ---- Users count ----
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $response["total_users"] = (int)$stmt->fetchColumn();

    // ---- Queries count ----
    $stmt = $pdo->query("SELECT COUNT(*) FROM queries");
    $response["total_queries"] = (int)$stmt->fetchColumn();

    // ---- Ratings count ----
    // (wrapped separately so it never crashes stats)
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM ratings");
        $response["total_ratings"] = (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        $response["total_ratings"] = 0;
    }

    echo json_encode($response);
    exit;

} catch (Exception $e) {
    // NEVER break frontend
    http_response_code(200);
    echo json_encode($response);
}
