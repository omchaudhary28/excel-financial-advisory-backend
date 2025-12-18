<?php
require_once 'cors.php';
require_once 'middleware_auth.php'; // Include authentication middleware

header("Content-Type: application/json; charset=UTF-8");
header('X-Content-Type-Options: nosniff');

// Handle pre-flight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

$errors = [];

// Basic validation
if (empty($data->name)) {
    $errors[] = "Name is required.";
}
if (empty($data->email) || !filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email is required.";
}
if (empty($data->subject)) {
    $errors[] = "Subject is required.";
}
if (empty($data->message)) {
    $errors[] = "Message is required.";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit();
}

// Get user ID from authenticated user data
$user_id = $GLOBALS['authenticated_user']['user_id'];

// The rest of the data comes from the POST body
$name = $data->name;
$email = $data->email;
$subject = $data->subject;
$message = $data->message;

// Prepare and execute statement to insert query
$stmt = $conn->prepare("INSERT INTO queries (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
if ($stmt === false) {
    error_log("Failed to prepare query insert statement: " . $conn->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'errors' => ["An internal error occurred."]]);
    exit();
}

$stmt->bind_param("issss", $user_id, $name, $email, $subject, $message);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Query submitted successfully!']);
} else {
    error_log("Failed to execute query insert statement: " . $stmt->error);
    http_response_code(500);
    echo json_encode(['success' => false, 'errors' => ["Failed to submit query."]]);
}

$stmt->close();
$conn->close();
?>