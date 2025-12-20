<?php

require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

define('JWT_SECRET', getenv('JWT_SECRET'));

if (!JWT_SECRET) {
    throw new Exception("JWT_SECRET not set");
}

function generateJWT(array $payload): string
{
    $payload['iat'] = time();
    $payload['exp'] = time() + 86400; // 24 hours

    return JWT::encode($payload, JWT_SECRET, 'HS256');
}
