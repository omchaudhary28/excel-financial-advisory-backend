<?php
// Absolute minimal, Render-safe CORS

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origin === 'https://excel-financial-advisory.vercel.app') {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// MUST exit early for preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
