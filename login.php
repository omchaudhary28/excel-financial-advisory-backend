<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

$input = json_decode(file_get_contents("php://input"), true);

$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing credentials"
    ]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, email, password, name, role
    FROM users
    WHERE email = :email
");
$stmt->execute([":email" => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password"
    ]);
    exit;
}

$token = generateJWT([
    "id"    => $user['id'],
    "email" => $user['email'],
    "role"  => $user['role']
]);

echo json_encode([
    "success" => true,
    "token"   => $token,
    "user"    => [
        "id"    => $user['id'],
        "email" => $user['email'],
        "name"  => $user['name'],
        "role"  => $user['role']
    ]
]);
