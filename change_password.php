<?php
require_once 'cors.php';
require_once 'middleware_auth.php'; // Include authentication middleware


header("Content-Type: application/json; charset=UTF-8");
header('X-Content-Type-Options: nosniff');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'db_connect.php';

// Check if user is logged in (handled by middleware_auth.php)
$authenticated_user = $GLOBALS['authenticated_user']; // Data from middleware_auth.php
if (!isset($authenticated_user['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Unauthorized: Please log in."]);
    exit;
}

$user_id = $authenticated_user['user_id'];
$data = json_decode(file_get_contents("php://input"));

$errors = [];

// Validate input
if (empty($data->current_password)) {
    $errors[] = "Current password is required.";
}
if (empty($data->new_password)) {
    $errors[] = "New password is required.";
}
if (empty($data->confirm_new_password)) {
    $errors[] = "Confirm new password is required.";
}

if ($data->new_password !== $data->confirm_new_password) {
    $errors[] = "New password and confirm new password do not match.";
}
if (strlen($data->new_password) < 6) {
    $errors[] = "New password must be at least 6 characters long.";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["success" => false, "errors" => $errors]);
    exit;
}

$current_password = $data->current_password;
$new_password = $data->new_password;

// Fetch user's current hashed password from DB
$stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
if (!$stmt) {
    http_response_code(500);
    error_log("Database error: " . $conn->error);
    echo json_encode(["success" => false, "message" => "An internal error occurred."]);
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "User not found."]);
    exit;
}

$user = $result->fetch_assoc();
$hashed_password = $user['password'];

// Verify current password
if (!password_verify($current_password, $hashed_password)) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Incorrect current password."]);
    exit;
}

// Hash new password
$new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Update password in database
$update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
if (!$update_stmt) {
    http_response_code(500);
    error_log("Database error: " . $conn->error);
    echo json_encode(["success" => false, "message" => "An internal error occurred."]);
    exit;
}
$update_stmt->bind_param("si", $new_hashed_password, $user_id);

if ($update_stmt->execute()) {
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Password changed successfully."]);
} else {
    http_response_code(500);
    error_log("Failed to change password: " . $update_stmt->error);
    echo json_encode(["success" => false, "message" => "An internal error occurred."]);
}

$update_stmt->close();
$stmt->close();
$conn->close();?>