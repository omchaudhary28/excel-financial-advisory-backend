<?php require_once __DIR__.'/cors.php'; ?>
<?php
// âœ… CORS (MUST be at the top)
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

// âœ… Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data['name']) ||
    empty($data['email']) ||
    empty($data['message'])
) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO queries (name, email, subject, message)
     VALUES (:name, :email, :subject, :message)"
);

$stmt->execute([
    ":name" => $data['name'],
    ":email" => $data['email'],
    ":subject" => $data['subject'] ?? null,
    ":message" => $data['message']
]);

echo json_encode([
    "success" => true,
    "message" => "Query submitted successfully"
]);

