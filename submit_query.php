<?php
require_once "cors.php";

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit;
}

$name    = trim($data["name"] ?? "");
$email   = trim($data["email"] ?? "");
$subject = trim($data["subject"] ?? "");
$message = trim($data["message"] ?? "");

if (!$name || !$email || !$message) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

try {
    $dsn = "pgsql:host=" . getenv("DB_HOST") .
           ";port=" . getenv("DB_PORT") .
           ";dbname=" . getenv("DB_NAME");

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

    echo json_encode(["success" => true, "message" => "Query submitted successfully"]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
