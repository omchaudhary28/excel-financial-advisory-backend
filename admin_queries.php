<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/middleware_auth.php';
require_once __DIR__ . '/db_connect.php';

header("Content-Type: application/json");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 🔐 Require ADMIN
authenticate(true);

// -------------------
// Fetch users (WITH ROLE)
// -------------------
$stmtUsers = $pdo->query(
    "SELECT id, name, email, phone, role, created_at
     FROM users
     ORDER BY created_at DESC"
);
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// -------------------
// Fetch queries
// -------------------
$stmtQueries = $pdo->query(
    "SELECT 
        q.id,
        q.user_id,
        q.name AS query_name,
        q.email AS query_email,
        q.subject,
        q.message,
        q.created_at,
        u.name AS user_name,
        u.email AS user_email,
        u.phone AS user_phone
     FROM queries q
     LEFT JOIN users u ON q.user_id = u.id
     ORDER BY q.created_at DESC"
);
$queries = $stmtQueries->fetchAll(PDO::FETCH_ASSOC);

// -------------------
// Response
// -------------------
echo json_encode([
    "success" => true,
    "users"   => $users,
    "queries" => $queries
]);
exit;
