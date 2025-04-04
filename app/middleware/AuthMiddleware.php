<?php
require_once 'app/helpers/JwtHelper.php';

class AuthMiddleware
{
    public static function validateToken()
    {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Không tìm thấy token'
            ]);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $payload = JwtHelper::validateToken($token);

        if (!$payload) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Token không hợp lệ hoặc đã hết hạn'
            ]);
            exit;
        }

        return $payload;
    }

    public static function requireAdmin()
    {
        $payload = self::validateToken();
        if ($payload['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'status' => 'error',
                'message' => 'Không có quyền truy cập'
            ]);
            exit;
        }
        return $payload;
    }
}
