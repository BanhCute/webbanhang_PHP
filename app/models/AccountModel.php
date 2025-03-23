<?php
require_once 'app/config/database.php';

class AccountModel
{
    private $id;
    private $username;
    private $password;
    private $role;
    private $created_at;

    public function __construct()
    {
        // Không cần khởi tạo kết nối ở đây nữa
        // vì đã có biến $conn từ database.php
    }

    // Đăng ký tài khoản mới
    public function register($username, $password)
    {
        global $conn; // Sử dụng biến $conn từ database.php
        try {
            // Kiểm tra username đã tồn tại chưa
            $stmt = $conn->prepare("SELECT id FROM account WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->rowCount() > 0) {
                return false; // Username đã tồn tại
            }

            // Mã hóa mật khẩu
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Thêm tài khoản mới
            $stmt = $conn->prepare("INSERT INTO account (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashedPassword]);
            return true;
        } catch (PDOException $e) {
            die("Lỗi đăng ký: " . $e->getMessage());
        }
    }

    // Đăng nhập
    public function login($username, $password)
    {
        global $conn;
        try {
            // Thêm debug để kiểm tra
            var_dump($username, $password);

            $stmt = $conn->prepare("SELECT * FROM account WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            return false;
        } catch (PDOException $e) {
            die("Lỗi đăng nhập: " . $e->getMessage());
        }
    }

    // Lấy thông tin user theo ID
    public function getUserById($id)
    {
        global $conn;
        try {
            $stmt = $conn->prepare("SELECT * FROM account WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            die("Lỗi lấy thông tin user: " . $e->getMessage());
        }
    }

    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE account SET password = ? WHERE id = ?";
        global $conn;
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute([$hashedPassword, $userId]);
            return true;
        } catch (PDOException $e) {
            die("Lỗi cập nhật mật khẩu: " . $e->getMessage());
        }
    }

    // Getters và Setters
    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
