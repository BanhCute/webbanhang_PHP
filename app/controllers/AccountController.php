<?php
require_once 'app/models/AccountModel.php';
require_once 'app/config/database.php';

class AccountController
{
    private $accountModel;
    private $db;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
        global $conn;
        $this->db = $conn;
    }

    // Hiển thị form đăng ký
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $errors = [];

            if (empty($username)) {
                $errors[] = "Vui lòng nhập tên đăng nhập";
            }

            if (empty($password)) {
                $errors[] = "Vui lòng nhập mật khẩu";
            }

            if ($password !== $confirm_password) {
                $errors[] = "Mật khẩu xác nhận không khớp";
            }

            if (empty($errors)) {
                try {
                    // Kiểm tra username đã tồn tại chưa
                    $stmt = $this->db->prepare("SELECT id FROM account WHERE username = ?");
                    $stmt->execute([$username]);
                    if ($stmt->fetch()) {
                        $_SESSION['error'] = 'Tên đăng nhập đã được sử dụng';
                    } else {
                        // Thêm user mới
                        $stmt = $this->db->prepare("
                            INSERT INTO account (username, password, role) 
                            VALUES (?, ?, 'user')
                        ");

                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt->execute([$username, $hashed_password]);

                        $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                        header('Location: ' . ROOT_URL . '/Account/login');
                        exit;
                    }
                } catch (PDOException $e) {
                    $_SESSION['error'] = 'Lỗi hệ thống, vui lòng thử lại sau';
                }
            } else {
                $_SESSION['error'] = implode("<br>", $errors);
            }
        }

        require_once ROOT_PATH . '/app/views/shares/header.php';
        require_once ROOT_PATH . '/app/views/account/register.php';
        require_once ROOT_PATH . '/app/views/shares/footer.php';
    }

    // Hiển thị form đăng nhập
    public function login()
    {
        $error = null;

        if (isset($_SESSION['user_id'])) {
            // Nếu đã đăng nhập, chuyển hướng theo role
            if ($_SESSION['role'] === 'admin') {
                header('Location: /T6-Sang/webbanhang/Product/list');
            } else {
                header('Location: /T6-Sang/webbanhang/Cart');
            }
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';

                if (empty($username) || empty($password)) {
                    throw new Exception('Vui lòng nhập đầy đủ thông tin');
                }

                // Kiểm tra tài khoản tồn tại - chỉ lấy các trường cần thiết
                $stmt = $this->db->prepare("SELECT id, username, password, role, created_at FROM account WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                // Kiểm tra mật khẩu với password_verify
                if ($user && password_verify($password, $user['password'])) {
                    // Lưu thông tin vào session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'] ?? 'user'; // Mặc định là user nếu role null

                    try {
                        // Lấy số lượng sản phẩm trong giỏ hàng từ bảng carts
                        $stmt = $this->db->prepare("SELECT SUM(quantity) as cart_count FROM carts WHERE user_id = ?");
                        $stmt->execute([$user['id']]);
                        $result = $stmt->fetch();
                        $_SESSION['cart_count'] = $result['cart_count'] ?? 0;
                    } catch (PDOException $e) {
                        $_SESSION['cart_count'] = 0;
                    }

                    // Chuyển hướng dựa vào role
                    if (strtolower($user['role']) === 'admin') {
                        header('Location: /T6-Sang/webbanhang/Product/list');
                        exit();
                    } else {
                        header('Location: /T6-Sang/webbanhang/Cart');
                        exit();
                    }
                } else {
                    throw new Exception('Tên đăng nhập hoặc mật khẩu không đúng');
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
                // Log lỗi để debug
                error_log("Login error: " . $e->getMessage());
            }
        }

        // Load view với thông báo lỗi
        include 'app/views/shares/header.php';
        include 'app/views/account/login.php';
        include 'app/views/shares/footer.php';
    }

    // Đăng xuất
    public function logout()
    {
        session_destroy();
        header('Location: ' . ROOT_URL . '/Account/login');
        exit;
    }

    // Kiểm tra đăng nhập
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // Kiểm tra quyền admin
    public function isAdmin()
    {
        return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
    }

    public function profile()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /T6-Sang/webbanhang/Account/login');
            exit();
        }

        try {
            // Lấy thông tin user
            $stmt = $this->db->prepare("SELECT id, username, role, created_at FROM account WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception('Không tìm thấy thông tin tài khoản');
            }

            // Lấy lịch sử đơn hàng
            $stmt = $this->db->prepare("
                SELECT o.*, 
                       (SELECT COUNT(*) FROM order_details od WHERE od.order_id = o.id) as total_items,
                       (SELECT SUM(od.price * od.quantity) FROM order_details od WHERE od.order_id = o.id) as total_amount
                FROM orders o 
                WHERE o.user_id = ? 
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $orders = $stmt->fetchAll();

            // Load view với dữ liệu
            include 'app/views/shares/header.php';
            include 'app/views/account/profile.php';
            include 'app/views/shares/footer.php';
        } catch (Exception $e) {
            error_log("Profile error: " . $e->getMessage());
            header('Location: /T6-Sang/webbanhang/Account/login');
            exit();
        }
    }

    public function changePassword()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Vui lòng đăng nhập để thực hiện chức năng này');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }

            if ($newPassword !== $confirmPassword) {
                throw new Exception('Mật khẩu xác nhận không khớp');
            }

            // Kiểm tra mật khẩu hiện tại
            $stmt = $this->db->prepare("SELECT password FROM account WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                throw new Exception('Mật khẩu hiện tại không đúng');
            }

            // Cập nhật mật khẩu mới
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE account SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $_SESSION['user_id']]);

            echo json_encode([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công'
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}
