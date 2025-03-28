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

        // Kiểm tra quyền admin ngay khi khởi tạo controller
        $this->checkAdminAccess();

        // Khởi tạo kết nối database
        global $conn;
        if (!isset($conn)) {
            require_once ROOT_PATH . '/app/config/database.php';
        }
        $this->db = $conn;

        // Khởi tạo ProductModel
        require_once ROOT_PATH . '/app/models/ProductModel.php';
        $this->productModel = new ProductModel($this->db);
    }

    // Thêm phương thức kiểm tra quyền admin
    private function checkAdminAccess()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang quản lý";
            header('Location: ' . ROOT_URL . '/Account/login');
            exit;
        }
    }

    public function product()
    {
        try {
            // Số sản phẩm trên mỗi trang
            $limit = 4;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Lấy category_id từ query string nếu có
            $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

            // Lấy danh sách categories cho form lọc
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
            $categories = $stmt->fetchAll();

            // Lấy tổng số sản phẩm (có filter theo category nếu có)
            if ($category_id) {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                $stmt->execute([$category_id]);
            } else {
                $stmt = $this->db->query("SELECT COUNT(*) FROM products");
            }
            $totalProducts = $stmt->fetchColumn();
            $totalPages = ceil($totalProducts / $limit);

            // Lấy danh sách sản phẩm có phân trang và lọc theo category
            $sql = "SELECT p.*, c.name as category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id";

            if ($category_id) {
                $sql .= " WHERE p.category_id = :category_id";
            }

            $sql .= " ORDER BY p.id DESC LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            if ($category_id) {
                $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $products = $stmt->fetchAll();

            // Load view
            require_once ROOT_PATH . '/app/views/admin/product/list.php';
        } catch (Exception $e) {
            $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
            header('Location: ' . ROOT_URL);
            exit;
        }
    }

    public function add()
    {
        try {
            // Lấy danh sách categories cho form
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
            $categories = $stmt->fetchAll();

            require_once ROOT_PATH . '/app/views/admin/product/add.php';
        } catch (Exception $e) {
            $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin/product');
            exit;
        }
    }

    public function edit($id)
    {
        try {
            // Debug
            error_log("Edit method called with ID: " . $id);

            // Lấy thông tin sản phẩm
            $stmt = $this->db->prepare("SELECT p.*, c.name as category_name 
                                      FROM products p 
                                      LEFT JOIN categories c ON p.category_id = c.id 
                                      WHERE p.id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();

            if (!$product) {
                $_SESSION['error'] = "Không tìm thấy sản phẩm";
                header('Location: ' . ROOT_URL . '/admin/product');
                exit;
            }

            // Lấy danh sách categories
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
            $categories = $stmt->fetchAll();

            // Debug
            error_log("Product data: " . print_r($product, true));
            error_log("Categories: " . print_r($categories, true));

            require_once ROOT_PATH . '/app/views/admin/product/edit.php';
        } catch (Exception $e) {
            error_log("Error in edit method: " . $e->getMessage());
            $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin/product');
            exit;
        }
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = $_POST['name'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $category_id = $_POST['category_id'];
                $image = isset($_FILES['image']) ? $_FILES['image'] : null;

                if ($this->productModel->addProduct($name, $description, $price, $category_id, $image)) {
                    $_SESSION['success'] = "Thêm sản phẩm thành công";
                } else {
                    throw new Exception("Không thể thêm sản phẩm");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
            }
        }
        header('Location: ' . ROOT_URL . '/admin/product');
        exit;
    }

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

                if ($this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image)) {
                    $_SESSION['success'] = "Cập nhật sản phẩm thành công";
                } else {
                    throw new Exception("Không thể cập nhật sản phẩm");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
            }
        }
        header('Location: ' . ROOT_URL . '/admin/product');
        exit;
    }

    public function delete($id)
    {
        try {
            if (!$id) {
                throw new Exception("ID sản phẩm không hợp lệ");
            }

            // Lấy thông tin sản phẩm để xóa ảnh
            $product = $this->productModel->getProductById($id);

            if ($this->productModel->deleteProduct($id)) {
                // Xóa file ảnh nếu có
                if ($product && !empty($product['image'])) {
                    $imagePath = ROOT_PATH . '/public/uploads/products/' . $product['image'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $_SESSION['success'] = "Xóa sản phẩm thành công";
            } else {
                throw new Exception("Không thể xóa sản phẩm");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
        }
        header('Location: ' . ROOT_URL . '/admin/product');
        exit;
    }

    public function category()
    {
        try {
            // Kiểm tra đăng nhập và quyền admin
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
                $_SESSION['error'] = "Bạn không có quyền truy cập";
                header('Location: ' . ROOT_URL . '/Account/login');
                exit;
            }

            // Phân trang
            $limit = 4;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Lấy tổng số danh mục
            $stmt = $this->db->query("SELECT COUNT(*) FROM categories");
            $totalCategories = $stmt->fetchColumn();
            $totalPages = ceil($totalCategories / $limit);

            // Lấy danh sách danh mục có phân trang
            $sql = "SELECT c.*, 
                    (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count 
                FROM categories c 
                ORDER BY c.id DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $categories = $stmt->fetchAll();

            require_once ROOT_PATH . '/app/views/admin/category/list.php';
        } catch (Exception $e) {
            $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin/category');
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

                // Debug
                error_log("Saving category: " . $name);

                $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
                if ($stmt->execute([$name])) {
                    $_SESSION['success'] = "Thêm danh mục thành công";
                } else {
                    throw new Exception("Không thể thêm danh mục");
                }
            } catch (Exception $e) {
                error_log("Error saving category: " . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
            }
        }
        header('Location: ' . ROOT_URL . '/admin/category');
        exit;
    }

    public function updateCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $name = trim($_POST['name']);

                // Debug
                error_log("Updating category ID: " . $id . " with name: " . $name);

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
                error_log("Error updating category: " . $e->getMessage());
                $_SESSION['error'] = $e->getMessage();
            }
        }
        header('Location: ' . ROOT_URL . '/admin/category');
        exit;
    }

    public function deleteCategory($id)
    {
        try {
            // Debug
            error_log("Deleting category ID: " . $id);

            if (!$id) {
                throw new Exception("ID danh mục không hợp lệ");
            }

            $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['success'] = "Xóa danh mục thành công";
            } else {
                throw new Exception("Không thể xóa danh mục");
            }
        } catch (Exception $e) {
            error_log("Error deleting category: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: ' . ROOT_URL . '/admin/category');
        exit;
    }

    public function user()
    {
        try {
            // Kiểm tra đăng nhập và quyền admin
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
                $_SESSION['error'] = "Bạn không có quyền truy cập";
                header('Location: ' . ROOT_URL . '/Account/login');
                exit;
            }

            // Phân trang
            $limit = 4;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;

            // Lấy tổng số người dùng
            $stmt = $this->db->query("SELECT COUNT(*) FROM account");
            $totalUsers = $stmt->fetchColumn();
            $totalPages = ceil($totalUsers / $limit);

            // Lấy danh sách người dùng có phân trang
            $sql = "SELECT * FROM account 
                ORDER BY created_at DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $users = $stmt->fetchAll();

            require_once ROOT_PATH . '/app/views/admin/user/list.php';
        } catch (Exception $e) {
            $_SESSION['error'] = "Có lỗi xảy ra: " . $e->getMessage();
            header('Location: ' . ROOT_URL . '/admin/user');
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
            // Kiểm tra quyền admin
            $this->checkAdminAccess();

            // Xử lý phân trang
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 5;
            $offset = ($page - 1) * $limit;

            // Xây dựng query
            $sql = "SELECT * FROM orders WHERE 1=1";
            $params = [];

            // Thêm điều kiện lọc
            if (isset($_GET['status']) && $_GET['status'] !== '') {
                $sql .= " AND status = :status";
                $params[':status'] = $_GET['status'];
            }

            if (!empty($_GET['from_date'])) {
                $sql .= " AND DATE(created_at) >= :from_date";
                $params[':from_date'] = $_GET['from_date'];
            }

            if (!empty($_GET['to_date'])) {
                $sql .= " AND DATE(created_at) <= :to_date";
                $params[':to_date'] = $_GET['to_date'];
            }

            // Đếm tổng số đơn hàng
            $countSql = "SELECT COUNT(*) FROM ($sql) as count_table";
            $stmt = $this->db->prepare($countSql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $totalOrders = $stmt->fetchColumn();
            $totalPages = ceil($totalOrders / $limit);

            // Thêm LIMIT và ORDER BY
            $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $orders = $stmt->fetchAll();

            require_once ROOT_PATH . '/app/views/admin/order/list.php';
        } catch (Exception $e) {
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
}
