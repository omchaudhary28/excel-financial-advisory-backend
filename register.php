<?php
require_once 'cors.php';
header("Content-Type: application/json; charset=UTF-8");

$errors = [];
$rawInput = file_get_contents("php://input");
if (empty($rawInput) && isset($GLOBALS['MOCK_INPUT_DATA'])) {
    $rawInput = $GLOBALS['MOCK_INPUT_DATA'];
}
$data = json_decode($rawInput);

// Check if data is valid
if (empty($data->name) || empty($data->email) || empty($data->password) || empty($data->confirm_password) || empty($data->phone)) {
    http_response_code(400);
    echo json_encode(["success" => false, "errors" => ["All fields are required."]]);
    if (isset($GLOBALS['IS_TESTING'])) return; exit;
}

$name = trim($data->name);
$email = trim($data->email);
$password = trim($data->password);
$confirm_password = trim($data->confirm_password);
$phone = trim($data->phone);

// Block reserved admin email
if (strtolower($email) === 'admin@gmail.com') {
    http_response_code(400);
    echo json_encode(["success" => false, "errors" => ["This email address is unavailable."]]);
    if (isset($GLOBALS['IS_TESTING'])) return; exit;
}

// Validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email is required.";
}
if (!preg_match('/^[0-9]{10}$/', $phone)) {
    $errors[] = "Invalid phone number format.";
}
if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters long.";
}
if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(["success" => false, "errors" => $errors]);
    if (isset($GLOBALS['IS_TESTING'])) return; exit;
}

require_once 'db_connect.php';

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$stmt) {
    error_log("Failed to prepare email check statement: " . $conn->error);
    http_response_code(500);
    echo json_encode(["success" => false, "errors" => ["Database error during email check."]]);
    if (isset($GLOBALS['IS_TESTING'])) return; exit;
}
$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    error_log("Failed to execute email check statement: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["success" => false, "errors" => ["Database error during email check execution."]]);
    if (isset($GLOBALS['IS_TESTING'])) return; exit;
}
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $errors[] = "Email address is already registered.";
    http_response_code(400);
    echo json_encode(["success" => false, "errors" => $errors]);
    $stmt->close();
    $conn->close();
    if (isset($GLOBALS['IS_TESTING'])) return; exit;
}
$stmt->close();

// If no errors, insert into database
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'user'; // Default role

$stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    error_log("Failed to prepare user insert statement: " . $conn->error);
    http_response_code(500);
    echo json_encode(["success" => false, "errors" => ["Database error during user insertion."]]);
    if (isset($GLOBALS['IS_TESTING'])) return; exit;
}
$stmt->bind_param("sssss", $name, $email, $hashed_password, $phone, $role);

if ($stmt->execute()) {
    http_response_code(201);
    error_log("Registration successful for email: " . $email);
} else {
    error_log("Failed to execute user insert statement: " . $stmt->error);
    http_response_code(500);
    echo json_encode(["success" => false, "errors" => ["Error during registration. Please try again later."]]);
}

$stmt->close();
$conn->close();
?>
