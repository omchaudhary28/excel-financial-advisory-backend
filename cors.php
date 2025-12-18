<?php header('Content-Type: application/json'); ?>
<?php require_once __DIR__.'/cors.php'; ?>
<?php
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if (["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}


