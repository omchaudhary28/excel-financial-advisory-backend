<?php
require_once "cors.php";

header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit;
}

$name = trim($data["name"] ?? "");
$email = trim($data["email"] ?? "");
$subject = trim($data["subject"] ?? "");
$message = trim($data["message"] ?? "");

if (!$name || !$email || !$message) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

/* PostgreSQL PDO connection */
$dsn = sprintf(
    "pgsql:host=%s;port=%s;dbname=%s;sslmode=require",
    getenv("DB_HOST"),
    getenv("DB_PORT"),
    getenv("DB_NAME")
);

try {
    $pdo = new PDO($dsn, getenv("DB_USER"), getenv("DB_PASSWORD"), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare(
        "INSERT INTO queries (name, email, subject, message)
         VALUES (:name, :email, :subject, :message)"
    );

    $stmt->execute([
        ":name" => $name,
        ":email" => $email,
        ":subject" => $subject,
        ":message" => $message
    ]);

    echo json_encode(["success" => true, "message" => "Query submitted"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
