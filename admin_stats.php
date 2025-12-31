<?php
require_once 'cors.php';
require_once 'db_connect.php';
require_once 'middleware_auth.php';

authenticate(true);

echo json_encode([
  "success" => true,
  "data" => [
    "total_users" => (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    "total_queries" => (int)$pdo->query("SELECT COUNT(*) FROM queries")->fetchColumn(),
    "total_ratings" => (int)$pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn()
  ]
]);
