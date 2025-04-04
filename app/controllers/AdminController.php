<?php

class AdminController
{
    private $productModel;
    private $db;
    private $categoryModel;

    public function __construct()
    {
        // Start session nếu chưa start
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Kiểm tra quyền admin
        $this->checkAdminAccess();

        // Khởi tạo kết nối database
        global $conn;
        $this->db = $conn;

        // Khởi tạo models
        require_once ROOT_PATH . '/app/models/ProductModel.php';
        require_once ROOT_PATH . '/app/models/CategoryModel.php';
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    private function checkAdminAccess()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang quản lý";
            header('Location: ' . ROOT_URL . '/Account/login');
            exit;
        }
    }

    // Trang chủ admin
    public function index()
    {
        try {
            // Lấy thống kê cơ bản
            $totalProducts = $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
            $totalCategories = $this->db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
            $totalAccounts = $this->db->query("SELECT COUNT(*) FROM account")->fetchColumn();
            $totalOrders = $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();

            require_once ROOT_PATH . '/app/views/admin/category/list.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin');
            exit;
        }
    }

    // Quản lý sản phẩm
    public function product()
    {
        try {
            // Lấy danh sách danh mục cho form lọc
            $stmtCategories = $this->db->prepare("SELECT * FROM categories ORDER BY name");
            $stmtCategories->execute();
            $categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

            // Xây dựng câu query với điều kiện lọc
            $sql = "SELECT p.*, c.name as category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id";

            $params = [];

            // Thêm điều kiện lọc theo danh mục nếu có
            if (!empty($_GET['category_id'])) {
                $sql .= " WHERE p.category_id = :category_id";
                $params[':category_id'] = $_GET['category_id'];
            }

            $sql .= " ORDER BY p.id DESC";

            // Thực thi query
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Load view với dữ liệu
            require_once ROOT_PATH . '/app/views/admin/product/list.php';
        } catch (Exception $e) {
            error_log("Lỗi trong AdminController::product: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin');
            exit;
        }
    }

    // Form thêm sản phẩm
    public function add()
    {
        try {
            // Lấy danh sách danh mục cho form thêm
            $sql = "SELECT * FROM categories ORDER BY name";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            require_once ROOT_PATH . '/app/views/admin/product/add.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin/product');
            exit;
        }
    }

    // Xử lý thêm sản phẩm
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = $_POST['name'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $category_id = $_POST['category_id'];

                // Xử lý upload ảnh
                $image = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $uploadDir = ROOT_PATH . '/public/uploads/products/'; // Thêm thư mục products

                    // Tạo thư mục nếu chưa tồn tại
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $fileName = uniqid() . '_' . $_FILES['image']['name'];
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                        $image = $fileName;
                    }
                }

                $sql = "INSERT INTO products (name, description, price, category_id, image) 
                        VALUES (:name, :description, :price, :category_id, :image)";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':category_id', $category_id);
                $stmt->bindParam(':image', $image);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Thêm sản phẩm thành công";
                } else {
                    $_SESSION['error'] = "Thêm sản phẩm thất bại";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        header('Location: ' . ROOT_URL . '/admin/product');
        exit;
    }

    // Form sửa sản phẩm
    public function edit($id)
    {
        try {
            $product = $this->productModel->getProductById($id);
            $categories = $this->categoryModel->getAllCategories();
            require_once ROOT_PATH . '/app/views/admin/product/edit.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin/product');
            exit;
        }
    }

    // Xử lý sửa sản phẩm
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $name = $_POST['name'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $category_id = $_POST['category_id'];
                $image = isset($_FILES['image']) ? $_FILES['image'] : null;

                $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);

                if ($result) {
                    $_SESSION['success'] = "Cập nhật sản phẩm thành công";
                } else {
                    $_SESSION['error'] = "Cập nhật sản phẩm thất bại";
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        header('Location: ' . ROOT_URL . '/admin/product');
        exit;
    }

    // Xóa sản phẩm
    public function delete($id)
    {
        try {
            if ($this->productModel->deleteProduct($id)) {
                $_SESSION['success'] = "Xóa sản phẩm thành công";
            } else {
                $_SESSION['error'] = "Xóa sản phẩm thất bại";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: ' . ROOT_URL . '/admin/product');
        exit;
    }

    public function category()
    {
        try {
            // Lấy danh sách categories kèm số lượng sản phẩm
            $sql = "SELECT c.*, 
                    COUNT(p.id) as product_count 
                    FROM categories c 
                    LEFT JOIN products p ON c.id = p.category_id 
                    GROUP BY c.id, c.name 
                    ORDER BY c.name";
            $categories = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            // Phân trang
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $total = count($categories);
            $totalPages = ceil($total / $limit);

            require_once ROOT_PATH . '/app/views/admin/category/list.php';
        } catch (Exception $e) {
            error_log("Lỗi trong AdminController::category: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin');
            exit;
        }
    }

    public function saveCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = trim($_POST['name']);
                if (empty($name)) {
                    throw new Exception("Tên danh mục không được để trống");
                }

                $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
                if ($stmt->execute([$name])) {
                    $_SESSION['success'] = "Thêm danh mục thành công";
                } else {
                    throw new Exception("Không thể thêm danh mục");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: ' . ROOT_URL . '/admin/category');
            exit;
        }
    }

    public function updateCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $name = trim($_POST['name']);
                if (empty($name)) {
                    throw new Exception("Tên danh mục không được để trống");
                }

                $stmt = $this->db->prepare("UPDATE categories SET name = ? WHERE id = ?");
                if ($stmt->execute([$name, $id])) {
                    $_SESSION['success'] = "Cập nhật danh mục thành công";
                } else {
                    throw new Exception("Không thể cập nhật danh mục");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: ' . ROOT_URL . '/admin/category');
            exit;
        }
    }

    public function deleteCategory($id)
    {
        try {
            // Kiểm tra xem có sản phẩm nào trong danh mục không
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                throw new Exception("Không thể xóa danh mục này vì có sản phẩm đang sử dụng");
            }

            $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['success'] = "Xóa danh mục thành công";
            } else {
                throw new Exception("Không thể xóa danh mục");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: ' . ROOT_URL . '/admin/category');
        exit;
    }

    public function user()
    {
        try {
            // Sửa lại để lấy từ bảng account
            $users = $this->db->query("SELECT * FROM account ORDER BY id DESC")->fetchAll();

            // Thêm biến page và totalPages cho phân trang
            $page = 1;
            $totalPages = 1;

            require_once ROOT_PATH . '/app/views/admin/user/list.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin');
            exit;
        }
    }

    public function saveUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = trim($_POST['username']);
                $password = $_POST['password'];
                $role = $_POST['role'];

                // Kiểm tra username đã tồn tại chưa
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM account WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("Tên đăng nhập đã tồn tại");
                }

                // Mã hóa mật khẩu
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $this->db->prepare("
                    INSERT INTO account (username, password, role, created_at) 
                    VALUES (?, ?, ?, NOW())
                ");

                if ($stmt->execute([$username, $hashedPassword, $role])) {
                    $_SESSION['success'] = "Thêm người dùng thành công";
                } else {
                    throw new Exception("Không thể thêm người dùng");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        header('Location: ' . ROOT_URL . '/admin/user');
        exit;
    }

    public function updateUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $username = trim($_POST['username']);
                $password = $_POST['password'];
                $role = $_POST['role'];

                // Kiểm tra username đã tồn tại chưa (trừ user hiện tại)
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM account WHERE username = ? AND id != ?");
                $stmt->execute([$username, $id]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("Tên đăng nhập đã tồn tại");
                }

                if (!empty($password)) {
                    // Nếu có nhập mật khẩu mới
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $this->db->prepare("
                        UPDATE account 
                        SET username = ?, password = ?, role = ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$username, $hashedPassword, $role, $id]);
                } else {
                    // Nếu không đổi mật khẩu
                    $stmt = $this->db->prepare("
                        UPDATE account 
                        SET username = ?, role = ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$username, $role, $id]);
                }

                $_SESSION['success'] = "Cập nhật người dùng thành công";
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        header('Location: ' . ROOT_URL . '/admin/user');
        exit;
    }

    public function deleteUser($id)
    {
        try {
            // Không cho phép xóa chính mình
            if ($id == $_SESSION['user_id']) {
                throw new Exception("Không thể xóa tài khoản đang đăng nhập");
            }

            $stmt = $this->db->prepare("DELETE FROM account WHERE id = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['success'] = "Xóa người dùng thành công";
            } else {
                throw new Exception("Không thể xóa người dùng");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: ' . ROOT_URL . '/admin/user');
        exit;
    }

    public function order()
    {
        try {
            // Lấy danh sách đơn hàng với phân trang
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $sql = "SELECT o.*, a.username 
                    FROM orders o 
                    LEFT JOIN account a ON o.user_id = a.id 
                    ORDER BY o.created_at DESC 
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            require_once ROOT_PATH . '/app/views/admin/order/list.php';
        } catch (Exception $e) {
            error_log("Lỗi trong AdminController::order: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin');
            exit;
        }
    }

    public function updateOrderStatus()
    {
        try {
            $this->checkAdminAccess();

            $orderId = $_POST['order_id'] ?? 0;
            $status = $_POST['status'] ?? 0;

            $sql = "UPDATE orders SET status = :status WHERE id = :order_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':status', $status, PDO::PARAM_INT);
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getOrderDetails($orderId)
    {
        try {
            $this->checkAdminAccess();

            // Lấy thông tin chi tiết đơn hàng
            $sql = "SELECT od.*, p.name as product_name 
                    FROM order_details od 
                    LEFT JOIN products p ON od.product_id = p.id 
                    WHERE od.order_id = :order_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll();

            // Tính tổng tiền
            $total = array_reduce($items, function ($sum, $item) {
                return $sum + ($item['price'] * $item['quantity']);
            }, 0);

            echo json_encode([
                'success' => true,
                'items' => $items,
                'total' => $total
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Thêm method mới cho dashboard
    public function dashboard()
    {
        try {
            // Thống kê doanh thu theo tháng
            $sql = "SELECT 
                    MONTH(o.created_at) as month,
                    COUNT(o.id) as total_orders,
                    SUM(o.total_amount) as revenue
                    FROM orders o 
                    WHERE YEAR(o.created_at) = YEAR(CURRENT_DATE)
                    GROUP BY MONTH(o.created_at)
                    ORDER BY month";
            $monthlyStats = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            // Thống kê tổng quan
            $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as avg_order_value
                    FROM orders";
            $overview = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);

            // Thống kê theo danh mục
            $sql = "SELECT 
                    c.name as category_name,
                    COUNT(DISTINCT o.id) as total_orders,
                    SUM(od.quantity * od.price) as revenue
                    FROM categories c
                    LEFT JOIN products p ON c.id = p.category_id
                    LEFT JOIN order_details od ON p.id = od.product_id
                    LEFT JOIN orders o ON od.order_id = o.id
                    GROUP BY c.id, c.name";
            $categoryStats = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            // Load view dashboard thay vì category
            require_once ROOT_PATH . '/app/views/admin/dashboard/index.php';
        } catch (Exception $e) {
            error_log("Lỗi trong AdminController::dashboard: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin');
            exit;
        }
    }
}
