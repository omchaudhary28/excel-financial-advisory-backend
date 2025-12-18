<?php header('Content-Type: application/json'); ?>
<?php require_once __DIR__.'/cors.php'; ?>
<?php
echo json_encode([
  "status" => "Backend running",
  "time" => time()
]);


