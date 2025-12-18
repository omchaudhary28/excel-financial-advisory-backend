<?php
require_once 'cors.php';
require_once 'middleware_auth.php';

header("Content-Type: application/json; charset=UTF-8");
header('X-Content-Type-Options: nosniff');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'db_connect.php';

// 🔐 Authorization check using JWT payload
$authenticated_user = $GLOBALS['authenticated_user'];

if (
    !isset($authenticated_user['id']) ||
    !isset($authenticated_user['role']) ||
    $authenticated_user['role'] !== 'admin'
) {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Forbidden: Admin access required."
    ]);
    exit;
}

// -------------------
// Fetch all users
// -------------------
$users = [];
$sql_users = "SELECT id, name, email, phone, created_at FROM users ORDER BY created_at DESC";
$result_users = $conn->query($sql_users);

if ($result_users) {
    while ($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    error_log("Error fetching users: " . $conn->error);
}

// -------------------
// Fetch all queries
// -------------------
$queries = [];
$sql_queries = "
    SELECT 
        q.id,
        q.user_id,
        q.name AS query_name,
        q.email AS query_email,
        q.subject,
        q.message,
        q.type,
        q.created_at,
        u.name AS user_name,
        u.email AS user_email,
        u.phone AS user_phone,
        u.created_at AS user_created_at
    FROM queries q
    LEFT JOIN users u ON q.user_id = u.id
    ORDER BY q.created_at DESC
";

$result_queries = $conn->query($sql_queries);

if ($result_queries) {
    while ($row = $result_queries->fetch_assoc()) {
        $queries[] = $row;
    }
} else {
    http_response_code(500);
    error_log("Error fetching queries: " . $conn->error);
    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch queries."
    ]);
    exit;
}

// -------------------
// Final response
// -------------------
http_response_code(200);
echo json_encode([
    "success" => true,
    "users"   => $users,
    "queries" => $queries
]);

$conn->close();
