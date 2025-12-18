<?php
/**
 * JWT utility functions
 * Uses HS256
 */

function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function generateJWT(array $payload, int $expirySeconds = 86400): string
{
    $secret = getenv('JWT_SECRET') ?: 'CHANGE_THIS_SECRET';

    $header = [
        'alg' => 'HS256',
        'typ' => 'JWT'
    ];

    $payload['iat'] = time();
    $payload['exp'] = time() + $expirySeconds;

    $base64Header  = base64UrlEncode(json_encode($header));
    $base64Payload = base64UrlEncode(json_encode($payload));

    $signature = hash_hmac(
        'sha256',
        $base64Header . '.' . $base64Payload,
        $secret,
        true
    );

    $base64Signature = base64UrlEncode($signature);

    return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
}
