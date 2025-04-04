<?php
require_once 'app/helpers/JwtHelper.php';

class AuthApiController
{
    private $db;

    public function __construct()
    {
        global $conn;
        $this->db = $conn;
    }

    public function login()
    {
        try {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['username']) || empty($data['password'])) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Username và password không được trống'
                ]);
                return;
            }

            $stmt = $this->db->prepare("SELECT * FROM account WHERE username = ?");
            $stmt->execute([$data['username']]);
            $user = $stmt->fetch();

            if ($user && password_verify($data['password'], $user['password'])) {
                $token = JwtHelper::generateToken($user['id'], $user['username'], $user['role']);

                echo json_encode([
                    'status' => 'success',
                    'message' => 'Đăng nhập thành công',
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'role' => $user['role']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Username hoặc password không đúng'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
