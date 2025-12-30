<?php
// ================= CORS =================
require_once 'cors.php';

// VERY IMPORTANT: handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ================= AUTH =================
require_once 'middleware_auth.php';
require_once 'db_connect.php';

// Ensure admin
$user = authenticate(true);

// ================= INPUT =================
$id = $_POST['id'] ?? null;
$approved = $_POST['approved'] ?? null;

if ($id === null || $approved === null) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing parameters"]);
    exit;
}

// ================= UPDATE =================
$stmt = $pdo->prepare(
    "UPDATE ratings SET approved = ? WHERE id = ?"
);

$success = $stmt->execute([
    (int)$approved,
    (int)$id
]);

echo json_encode([
    "success" => $success
]);
