<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';

$input = json_decode(file_get_contents("php://input"), true);

$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing fields"
    ]);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare(
        "INSERT INTO users (email, password) VALUES (:email, :password)"
    );
    $stmt->execute([
        ":email" => $email,
        ":password" => $hash
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Registration successful"
    ]);
} catch (PDOException $e) {
    http_response_code(409);
    echo json_encode([
        "success" => false,
        "message" => "Email already exists"
    ]);
}
exit;
