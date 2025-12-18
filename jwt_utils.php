<?php header('Content-Type: application/json'); ?>
<?php require_once __DIR__.'/cors.php'; ?>
<?php
class JWT {

    private static $secret = 'dadnancjecedddosjcsncsjsliedidddefsfnurfu@ef4def4';

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    public static function generate(array $payload, int $expiry = 86400) {
        $header = self::base64UrlEncode(json_encode([
            "typ" => "JWT",
            "alg" => "HS256"
        ]));

        $payload['iat'] = time();
        $payload['exp'] = time() + $expiry;

        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payloadEncoded", self::$secret, true)
        );

        return "$header.$payloadEncoded.$signature";
    }

    public static function verify(string $token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;

        [$header, $payload, $signature] = $parts;

        $expected = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", self::$secret, true)
        );

        if (!hash_equals($expected, $signature)) return false;

        $data = json_decode(self::base64UrlDecode($payload), true);
        if (!$data || $data['exp'] < time()) return false;

        return $data;
    }

    public static function getBearerToken() {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\\s(\\S+)/', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
?>

