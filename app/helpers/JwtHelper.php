<?php
class JwtHelper
{
    private static $secret_key = 'your_secret_key_123'; // Thay đổi key này
    private static $algorithm = 'HS256';

    public static function generateToken($user_id, $username, $role)
    {
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ]);

        $payload = json_encode([
            'user_id' => $user_id,
            'username' => $username,
            'role' => $role,
            'exp' => time() + (60 * 60) // Token hết hạn sau 1 giờ
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $base64UrlHeader . "." . $base64UrlPayload,
            self::$secret_key,
            true
        );

        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    public static function validateToken($token)
    {
        try {
            $tokenParts = explode('.', $token);
            if (count($tokenParts) != 3) {
                return false;
            }

            $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[0]));
            $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
            $signatureProvided = $tokenParts[2];

            // Verify signature
            $base64UrlHeader = $tokenParts[0];
            $base64UrlPayload = $tokenParts[1];
            $signature = hash_hmac(
                'sha256',
                $base64UrlHeader . "." . $base64UrlPayload,
                self::$secret_key,
                true
            );
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            if ($base64UrlSignature !== $signatureProvided) {
                return false;
            }

            $payload = json_decode($payload, true);

            // Check if token is expired
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return false;
            }

            return $payload;
        } catch (Exception $e) {
            return false;
        }
    }
}
