<?php
// ---------- CORS ----------
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

$user = verifyJWT();
$userId = $user['id'];

if (!isset($_FILES['avatar'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No file uploaded"]);
    exit;
}

$uploadDir = __DIR__ . "/uploads/avatars/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
$filename = "user_" . $userId . "." . $ext;
$filepath = $uploadDir . $filename;
$dbPath = "/uploads/avatars/" . $filename;

move_uploaded_file($_FILES['avatar']['tmp_name'], $filepath);

// Save path in DB
$stmt = $pdo->prepare("UPDATE users SET avatar = :avatar WHERE id = :id");
$stmt->execute([
    ':avatar' => $dbPath,
    ':id' => $userId
]);

echo json_encode([
    "success" => true,
    "avatar" => $dbPath
]);
