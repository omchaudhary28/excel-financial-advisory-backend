<?php
require_once __DIR__.'/db.php';
require_once __DIR__.'/jwt_utils.php';

$user = verifyJWT();
if ($user['role'] !== 'admin') exit;

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare(
  "UPDATE feedback SET approved = :a WHERE id = :id"
);
$stmt->execute([
  ":a" => (bool)$data['approved'],
  ":id" => (int)$data['id']
]);

echo json_encode(["success" => true]);
