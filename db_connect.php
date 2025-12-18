<?php
// Database configuration
// Check if running on localhost (WAMP) or Production (InfinityFree)
if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
    // Local settings (WAMP default)
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "excel_financial"; // You must create this database in your local phpMyAdmin
} else {
    // Production settings (InfinityFree)
    $servername = "sql100.infinityfree.com"; 
    $username   = "if0_40705804";            
    $password   = "vV42iQJHSTgFHlm";           
    $dbname     = "if0_40705804_excel_financial"; 
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    header('X-Content-Type-Options: nosniff');
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit;
}
?>