<?php
require_once 'cors.php';

http_response_code(410); // 410 = Gone (intentionally disabled)

echo json_encode([
    "error" => "Session-based authentication has been deprecated.",
    "message" => "This endpoint is no longer in use. JWT authentication is now required."
]);
