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
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $errors = [];

            // Kiểm tra email rỗng
            if (empty($username)) {
                $errors[] = "Vui lòng nhập email";
            } elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
                // Kiểm tra định dạng email hợp lệ
                $errors[] = "Email không hợp lệ";
            } elseif (!str_ends_with($username, '@gmail.com')) {
                // Kiểm tra email có phải Gmail không
                $errors[] = "Vui lòng sử dụng email Gmail (example@gmail.com)";
            }

            // Kiểm tra mật khẩu rỗng
            if (empty($password)) {
                $errors[] = "Vui lòng nhập mật khẩu";
            }

            // Kiểm tra xác nhận mật khẩu
            if ($password !== $confirm_password) {
                $errors[] = "Mật khẩu xác nhận không khớp";
            }

            if (empty($errors)) {
                try {
                    // Kiểm tra email đã tồn tại chưa
                    $stmt = $this->db->prepare("SELECT id FROM account WHERE username = ?");
                    $stmt->execute([$username]);
                    if ($stmt->fetch()) {
                        $_SESSION['error'] = 'Email đã được sử dụng';
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
        // Nếu đã đăng nhập thì chuyển về trang chủ
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT_URL);
            exit;
        }

        // Kiểm tra nếu có thông báo lỗi từ AdminController
        $error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
        unset($_SESSION['error']); // Xóa thông báo lỗi sau khi lấy

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = $_POST['username'];
                $password = $_POST['password'];

                if (empty($username) || empty($password)) {
                    throw new Exception('Vui lòng nhập đầy đủ thông tin');
                }

                $stmt = $this->db->prepare("SELECT * FROM account WHERE username = ?");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    // Lấy số lượng sản phẩm trong giỏ hàng
                    $stmt = $this->db->prepare("SELECT SUM(quantity) as total FROM carts WHERE user_id = ?");
                    $stmt->execute([$user['id']]);
                    $result = $stmt->fetch();
                    $_SESSION['cart_count'] = $result['total'] ?? 0;

                    // Nếu là admin và đang cố truy cập trang admin trước đó
                    if ($user['role'] === 'admin' && isset($_SESSION['attempted_admin_access'])) {
                        unset($_SESSION['attempted_admin_access']);
                        header('Location: ' . ROOT_URL . '/admin/product');
                        exit;
                    }

                    header('Location: ' . ROOT_URL);
                    exit;
                } else {
                    throw new Exception('Tên đăng nhập hoặc mật khẩu không đúng');
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }

        // Load view
        require_once ROOT_PATH . '/app/views/account/login.php';
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
    public function forgotPassword()
    {
        require_once ROOT_PATH . '/app/views/account/forgot_password.php';
    }

    public function sendResetCode()
    {
        try {
            $email = $_POST['email'] ?? '';

            // Kiểm tra email có tồn tại
            $sql = "SELECT id FROM account WHERE username = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            if (!$stmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email không tồn tại trong hệ thống'
                ]);
                return;
            }

            // Tạo mã xác nhận ngẫu nhiên
            $resetCode = sprintf("%06d", mt_rand(0, 999999));

            // Lưu mã xác nhận và thời gian hết hạn vào database
            $sql = "UPDATE account SET 
                reset_code = :reset_code,
                reset_code_expire = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                WHERE username = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':reset_code', $resetCode);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            // Gửi email
            require_once ROOT_PATH . '/vendor/autoload.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'truongnga252003@gmail.com'; // Email của bạn
                $mail->Password = 'psgm fxee eodl wkrz'; // Mật khẩu ứng dụng Gmail
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('truongnga252003@gmail.com', 'Shop Bảo Anh');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Mã xác nhận đặt lại mật khẩu';
                $mail->Body = "
                <h2>Đặt lại mật khẩu</h2>
                <p>Mã xác nhận của bạn là: <strong>$resetCode</strong></p>
                <p>Mã này sẽ hết hạn sau 15 phút.</p>
            ";

                $mail->send();

                echo json_encode([
                    'success' => true,
                    'message' => 'Đã gửi mã xác nhận đến email của bạn'
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không thể gửi email: ' . $mail->ErrorInfo
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function resetPassword()
    {
        try {
            $email = $_POST['email'] ?? '';
            $resetCode = $_POST['reset_code'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Kiểm tra mật khẩu mới
            if ($newPassword !== $confirmPassword) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Mật khẩu xác nhận không khớp'
                ]);
                return;
            }

            // Kiểm tra mã xác nhận
            $sql = "SELECT id FROM account 
                WHERE username = :email 
                AND reset_code = :reset_code 
                AND reset_code_expire > NOW()";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':reset_code', $resetCode);
            $stmt->execute();

            if (!$stmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Mã xác nhận không đúng hoặc đã hết hạn'
                ]);
                return;
            }

            // Cập nhật mật khẩu mới
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE account SET 
                password = :password,
                reset_code = NULL,
                reset_code_expire = NULL
                WHERE username = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':password', $hashedPassword);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'message' => 'Đặt lại mật khẩu thành công'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
}
