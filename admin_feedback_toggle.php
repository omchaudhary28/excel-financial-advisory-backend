<?php
require_once 'cors.php';
require_once 'db_connect.php';
require_once 'middleware_auth.php';

authenticate(true);

$id = $_POST['id'] ?? null;
$approved = $_POST['approved'] ?? null;

if ($id === null || $approved === null) {
  http_response_code(400);
  echo json_encode(["success" => false]);
  exit;
}

$stmt = $pdo->prepare(
  "UPDATE feedback SET approved = ? WHERE id = ?"
);

$stmt->execute([(int)$approved, (int)$id]);

echo json_encode(["success" => true]);
