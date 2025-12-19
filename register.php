<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/db.php";

/* ✅ Read JSON body */
$input = json_decode(file_get_contents("php://input"), true);

$name             = trim($input['name'] ?? '');
$email            = trim($input['email'] ?? '');
$password         = $input['password'] ?? '';
$confirm_password = $input['confirm_password'] ?? '';
$phone            = trim($input['phone'] ?? '');

if ($name === '' || $email === '' || $password === '' || $confirm_password === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields"
    ]);
    exit;
}

if ($password !== $confirm_password) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Passwords do not match"
    ]);
    exit;
}

/* ✅ Check if email already exists */
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$stmt->execute([":email" => $email]);

if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode([
        "success" => false,
        "message" => "Email already registered"
    ]);
    exit;
}

/* ✅ Hash password */
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

/* ✅ Insert user */
$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password, phone, role)
     VALUES (:name, :email, :password, :phone, 'user')"
);

$stmt->execute([
    ":name"     => $name,
    ":email"    => $email,
    ":password" => $hashedPassword,
    ":phone"    => $phone
]);

echo json_encode([
    "success" => true,
    "message" => "Registration successful"
]);
