<?php
require_once "cors.php";
require_once "db.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["email"], $data["password"])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing credentials"
    ]);
    exit;
}

$email = trim($data["email"]);
$password = $data["password"];

try {
    $stmt = $pdo->prepare("
        SELECT id, name, email, password, role
        FROM users
        WHERE email = :email
        LIMIT 1
    ");
    $stmt->execute(["email" => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user["password"])) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email or password"
        ]);
        exit;
    }

    // ✅ Generate token (NO DB STORAGE)
    $token = bin2hex(random_bytes(32));

    echo json_encode([
        "success" => true,
        "token" => $token,
        "user" => [
            "id" => $user["id"],
            "name" => $user["name"],
            "email" => $user["email"],
            "role" => $user["role"]
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error"
    ]);
}
