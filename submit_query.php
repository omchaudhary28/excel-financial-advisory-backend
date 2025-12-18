<?php
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare(
    "INSERT INTO queries (name,email,subject,message)
     VALUES (?,?,?,?)"
);

$stmt->execute([
    $data['name'],
    $data['email'],
    $data['subject'],
    $data['message']
]);

echo json_encode(["success" => true, "message" => "Query submitted"]);
