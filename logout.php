<?php
require_once 'cors.php';

http_response_code(410); // 410 = Gone (intentionally disabled)

echo json_encode([
    "error" => "Session-based logout has been deprecated.",
    "message" => "JWT authentication does not require server-side logout."
]);
