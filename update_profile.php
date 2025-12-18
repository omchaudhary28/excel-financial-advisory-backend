<?php
require_once 'cors.php';
require_once 'middleware_auth.php'; // Include authentication middleware

// CORS headers - MUST be sent before any other output
header("Content-Type: application/json; charset=UTF-8");
header('X-Content-Type-Options: nosniff');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
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

// Log received data for debugging
error_log("Received data for profile update: " . print_r($data, true));
// error_log("User ID from JWT: " . $user_id); // REMOVED: No longer JWT

$errors = [];

// Validate input
if (empty($data->name)) {
    $errors[] = "Name is required.";
}
if (empty($data->phone)) {
    $errors[] = "Phone number is required.";
} elseif (!preg_match("/^[0-9]{10}$/", $data->phone)) {
    $errors[] = "Invalid phone number format. Please enter a 10-digit number.";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["success" => false, "errors" => $errors]);
    exit;
}

$name = trim($data->name);
$phone = trim($data->phone);

// Update user in database
$stmt = $conn->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
if (!$stmt) {
    error_log("Database prepare statement failed: " . $conn->error);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "An internal error occurred."]);
    exit;
}

$stmt->bind_param("ssi", $name, $phone, $user_id);

if ($stmt->execute()) {
    // Re-fetch user data to send back the most current information
    $fetch_stmt = $conn->prepare("SELECT id, name, email, role, phone, created_at FROM users WHERE id = ?");
    if (!$fetch_stmt) {
        error_log("Database prepare statement for fetch failed: " . $conn->error);
        // Continue without user data in response, or handle more robustly
    } else {
        $fetch_stmt->bind_param("i", $user_id);
        $fetch_stmt->execute();
        $result = $fetch_stmt->get_result();
        $updated_user = $result->fetch_assoc();
        $fetch_stmt->close();
    }
    

    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Profile updated successfully.", "user" => $updated_user ?? null]);
} else {
    error_log("Database execute statement failed: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "An internal error occurred."]);
}

$stmt->close();
$conn->close();

} catch (Throwable $e) {
    error_log("Unhandled error in update_profile.php: " . $e->getMessage() . " on line " . $e->getLine());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "An unexpected server error occurred."]);
}
?>