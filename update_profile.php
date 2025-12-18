<?php header('Content-Type: application/json'); ?>
<?php require_once __DIR__.'/cors.php'; ?>
<?php
require "db.php";

$headers = getallheaders();
$token = str_replace("Bearer ", "", $headers['Authorization'] ?? "");

$stmt = $pdo->prepare("SELECT id FROM users WHERE token=?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare(
    "UPDATE users SET name=?, phone=? WHERE id=?"
);

$stmt->execute([
    $data['name'],
    $data['phone'],
    $user['id']
]);

echo json_encode(["success" => true, "message" => "Profile updated"]);


