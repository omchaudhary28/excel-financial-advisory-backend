<?php
require_once "db.php";

header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$name  = $data["name"] ?? null;
$email = $data["email"] ?? null;
$pass  = $data["password"] ?? null;

if (!$name || !$email || !$pass) {
    echo json_encode([
        "success" => false,
        "message" => "Missing fields"
    ]);
    exit;
}

$hashed = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
);

try {
    $stmt->execute([$name, $email, $hashed]);
    echo json_encode([
        "success" => true,
        "message" => "Registration successful"
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Email already exists"
    ]);
}
