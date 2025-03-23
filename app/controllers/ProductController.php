<?php
require_once 'app/config/config.php';
require_once 'app/models/ProductModel.php';
require_once 'app/helpers/SessionHelper.php';

class ProductController
{
    private $productModel;
    private $categoryModel;
    private $db;


    public function __construct()
    {
        // Kết nối databaseimage.png
        global $conn;
        require_once __DIR__ . '/../../app/config/database.php';
        $this->db = $conn;

        // Khởi tạo models
        require_once __DIR__ . '/../models/ProductModel.php';
        require_once __DIR__ . '/../models/CategoryModel.php';
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    private function checkConnection()
    {
        if (!$this->db) {
            throw new Exception("Mất kết nối database");
        }
    }

    public function index()
    {
        $filters = [
            'category' => $_GET['category'] ?? null,
            'search' => $_GET['search'] ?? null,
            'sort' => $_GET['sort'] ?? null
        ];

        // Debug log để kiểm tra filters
        error_log("Applied filters: " . print_r($filters, true));

        $products = $this->productModel->getAllProducts($filters);
        $categories = $this->categoryModel->getAllCategories();

        // Debug log để kiểm tra số lượng sản phẩm
        error_log("Number of products returned: " . count($products));

        require_once __DIR__ . '/../views/shares/header.php';

        // Nếu là admin thì hiện view quản lý, ngược lại hiện view user
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            require_once __DIR__ . '/../views/product/list.php';
        } else {
            require_once __DIR__ . '/../views/user/product/list.php';
        }

        require_once __DIR__ . '/../views/shares/footer.php';
    }

    public function add()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /T6-Sang/webbanhang/Product/index');
            exit();
        }

        $errors = [];
        $categories = $this->categoryModel->getAllCategories();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? '';

            // Xử lý upload ảnh
            $image = $_FILES['image'] ?? null;
            $imagePath = '';

            if (empty($name)) {
                $errors[] = "Vui lòng nhập tên sản phẩm";
            }
            if (empty($price)) {
                $errors[] = "Vui lòng nhập giá sản phẩm";
            }
            if (empty($category_id)) {
                $errors[] = "Vui lòng chọn danh mục";
            }

            // Xử lý upload ảnh nếu có
            if ($image && $image['tmp_name']) {
                $targetDir = "public/uploads/products/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $imageFileType = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
                $imageName = uniqid() . '.' . $imageFileType;
                $targetPath = $targetDir . $imageName;

                // Kiểm tra định dạng ảnh
                $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
                if (!in_array($imageFileType, $allowTypes)) {
                    $errors[] = "Chỉ chấp nhận file ảnh JPG, JPEG, PNG & GIF";
                }

                // Upload file
                if (empty($errors) && !move_uploaded_file($image['tmp_name'], $targetPath)) {
                    $errors[] = "Có lỗi xảy ra khi upload ảnh";
                } else {
                    $imagePath = $imageName;
                }
            }

            if (empty($errors)) {
                $data = [
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'category_id' => $category_id,
                    'image' => $imagePath
                ];

                if ($this->productModel->addProduct($name, $description, $price, $category_id, $imagePath)) {
                    $_SESSION['success_message'] = "Thêm sản phẩm thành công!";
                    header('Location: /T6-Sang/webbanhang/Product/index');
                    exit();
                } else {
                    $errors[] = "Có lỗi xảy ra khi thêm sản phẩm";
                }
            }
        }

        require_once __DIR__ . '/../views/shares/header.php';
        require_once __DIR__ . '/../views/product/add.php';
        require_once __DIR__ . '/../views/shares/footer.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = $_POST['name'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $category_id = $_POST['category_id'];

                // Xử lý ảnh
                $image = isset($_FILES['image']) ? $_FILES['image'] : null;

                // Gọi phương thức addProduct với đầy đủ 5 tham số
                if ($this->productModel->addProduct($name, $description, $price, $category_id, $image)) {
                    $_SESSION['success'] = "Thêm sản phẩm thành công";
                } else {
                    throw new Exception("Không thể thêm sản phẩm");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            }

            header('Location: ' . ROOT_URL . '/Product');
            exit;
        }
    }

    public function edit($id)
    {
        // Kiểm tra quyền admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /T6-Sang/webbanhang/Product/index');
            exit();
        }
        if ($id === null) {
            die("Không tìm thấy ID sản phẩm");
        }

        try {
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                die("Không tìm thấy sản phẩm");
            }

            $categories = $this->categoryModel->getAllCategories();
            include ROOT_PATH . '/app/views/product/edit.php';
        } catch (Exception $e) {
            die("Lỗi: " . $e->getMessage());
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];

            // Xử lý ảnh
            $image = isset($_FILES['image']) ? $_FILES['image'] : null;

            if ($this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image)) {
                $_SESSION['success'] = "Cập nhật sản phẩm thành công";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật sản phẩm";
            }

            header('Location: ' . ROOT_URL . '/Product');
            exit;
        }
    }

    public function delete($id)
    {
        // Kiểm tra quyền admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('Location: /T6-Sang/webbanhang/Product/index');
            exit();
        }
        if ($id === null) {
            die("Không tìm thấy ID sản phẩm");
        }

        try {
            $result = $this->productModel->deleteProduct($id);
            if ($result) {
                header('Location: /T6-Sang/webbanhang/product');
                exit;
            } else {
                die("Không thể xóa sản phẩm");
            }
        } catch (Exception $e) {
            die("Lỗi: " . $e->getMessage());
        }
    }

    public function search()
    {
        try {
            $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
            $model = new ProductModel($this->db);
            $products = $model->searchProducts($keyword);

            // Trả về HTML của các rows
            foreach ($products as $product) {
                echo '<tr>';
                echo '<td>' . $product['id'] . '</td>';
                echo '<td><img src="/T6-Sang/webbanhang/public/uploads/products/' .
                    ($product['image'] ?? 'no-image.jpg') .
                    '" class="rounded" style="width: 80px; height: 80px; object-fit: cover;"></td>';
                echo '<td><div class="fw-bold">' . $product['name'] . '</div>';
                echo '<small class="text-muted">' . substr($product['description'], 0, 50) . '...</small></td>';
                echo '<td class="fw-bold text-primary">' . number_format($product['price'], 0, ',', '.') . ' đ</td>';
                echo '<td><span class="badge bg-info">' . $product['category_name'] . '</span></td>';
                echo '<td>';
                echo '<a href="/T6-Sang/webbanhang/product/edit/' . $product['id'] .
                    '" class="btn btn-warning btn-sm me-1"><i class="fas fa-edit"></i></a>';
                echo '<button onclick="xoaSanPham(' . $product['id'] .
                    ')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>';
                echo '</td>';
                echo '</tr>';
            }
        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }

    public function getAll()
    {
        try {
            $model = new ProductModel($this->db);
            $products = $model->getProducts();

            // Trả về HTML tương tự như method search
            foreach ($products as $product) {
                // Code hiển thị giống như trong search()
            }
        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }

    public function cart()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['error'] = 'Vui lòng đăng nhập để xem giỏ hàng';
                header('Location: ' . ROOT_URL . '/Account/login');
                exit;
            }

            // Lấy sản phẩm trong giỏ hàng
            $stmt = $this->db->prepare("
                SELECT c.*, p.name, p.price, p.image, 
                       (p.price * c.quantity) as subtotal,
                       p.id as product_id
                FROM carts c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?
                ORDER BY c.created_at DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $cartItems = $stmt->fetchAll();

            // Tính tổng tiền
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['subtotal'];
            }

            // Debug
            error_log("Cart Items: " . print_r($cartItems, true));
            error_log("Total: " . $total);

            require_once ROOT_PATH . '/app/views/shares/header.php';
            require_once ROOT_PATH . '/app/views/product/cart.php';
            require_once ROOT_PATH . '/app/views/shares/footer.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/Product/list');
            exit;
        }
    }

    public function addToCart()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Vui lòng đăng nhập để thêm vào giỏ hàng');
            }

            $productId = $_POST['product_id'] ?? null;
            $quantity = intval($_POST['quantity'] ?? 1);
            $userId = $_SESSION['user_id'];

            if (!$productId) {
                throw new Exception('Thiếu thông tin sản phẩm');
            }

            // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
            $stmt = $this->db->prepare("SELECT * FROM carts WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            $existingItem = $stmt->fetch();

            if ($existingItem) {
                // Nếu sản phẩm đã tồn tại, cập nhật số lượng
                $stmt = $this->db->prepare("
                    UPDATE carts 
                    SET quantity = quantity + ? 
                    WHERE user_id = ? AND product_id = ?
                ");
                $stmt->execute([$quantity, $userId, $productId]);
            } else {
                // Nếu sản phẩm chưa tồn tại, thêm mới
                $stmt = $this->db->prepare("
                    INSERT INTO carts (user_id, product_id, quantity) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$userId, $productId, $quantity]);
            }

            // Lấy tổng số lượng trong giỏ hàng
            $stmt = $this->db->prepare("
                SELECT SUM(quantity) as total 
                FROM carts 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            $cartCount = $result['total'] ?? 0;

            $_SESSION['cart_count'] = $cartCount;

            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng',
                'cartCount' => $cartCount
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

    public function updateCart()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Vui lòng đăng nhập để cập nhật giỏ hàng');
            }

            $productId = $_POST['product_id'] ?? null;
            $quantity = intval($_POST['quantity'] ?? 0);
            $userId = $_SESSION['user_id'];

            if ($quantity > 0) {
                // Cập nhật số lượng
                $stmt = $this->db->prepare("
                    UPDATE carts 
                    SET quantity = ? 
                    WHERE user_id = ? AND product_id = ?
                ");
                $stmt->execute([$quantity, $userId, $productId]);
            } else {
                // Xóa sản phẩm khỏi giỏ hàng
                $stmt = $this->db->prepare("
                    DELETE FROM carts 
                    WHERE user_id = ? AND product_id = ?
                ");
                $stmt->execute([$userId, $productId]);
            }

            // Lấy tổng số lượng mới trong giỏ hàng
            $stmt = $this->db->prepare("
                SELECT SUM(quantity) as total 
                FROM carts 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch();
            $cartCount = intval($result['total'] ?? 0);

            // Cập nhật session
            $_SESSION['cart_count'] = $cartCount;

            // Trả về response với số lượng mới
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật giỏ hàng thành công',
                'cartCount' => $cartCount
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

    public function removeFromCart()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Vui lòng đăng nhập để thực hiện chức năng này');
            }

            if (!isset($_POST['product_id'])) {
                throw new Exception('Thiếu thông tin sản phẩm');
            }

            $productId = (int)$_POST['product_id'];
            unset($_SESSION['cart'][$productId]);

            $_SESSION['success'] = 'Đã xóa sản phẩm khỏi giỏ hàng';
            header('Location: ' . ROOT_URL . '/Product/cart');
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/Product/cart');
            exit;
        }
    }

    public function checkout()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT_URL . '/Account/login');
            exit;
        }

        try {
            // Lấy thông tin giỏ hàng kèm hình ảnh sản phẩm
            $stmt = $this->db->prepare("
                SELECT c.*, p.name, p.price, p.image, (c.quantity * p.price) as subtotal
                FROM carts c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $cartItems = $stmt->fetchAll();

            // Tính tổng tiền
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += $item['subtotal'];
            }

            require_once ROOT_PATH . '/app/views/shares/header.php';
            require_once ROOT_PATH . '/app/views/product/checkout.php';
            require_once ROOT_PATH . '/app/views/shares/footer.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/Product/cart');
            exit;
        }
    }

    public function processCheckout()
    {
        try {
            // Debug log
            error_log("processCheckout started");

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                throw new Exception('Giỏ hàng trống');
            }

            // Validate input
            if (empty($_POST['name']) || empty($_POST['phone']) || empty($_POST['address'])) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }

            // Bắt đầu thêm đơn hàng
            $sql = "INSERT INTO orders (name, phone, address) VALUES (:name, :phone, :address)";

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':name' => $_POST['name'],
                ':phone' => $_POST['phone'],
                ':address' => $_POST['address']
            ]);

            if (!$result) {
                throw new Exception("Không thể tạo đơn hàng");
            }

            $orderId = $this->db->lastInsertId();
            error_log("Order created with ID: " . $orderId);

            // Thêm chi tiết đơn hàng
            $sql = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                    VALUES (:order_id, :product_id, :quantity, :price)";

            $stmt = $this->db->prepare($sql);

            foreach ($_SESSION['cart'] as $productId => $item) {
                $result = $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $productId,
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);

                if (!$result) {
                    // Log lỗi chi tiết
                    error_log("Failed to insert order detail: " . print_r($this->db->errorInfo(), true));
                    throw new Exception("Không thể thêm chi tiết đơn hàng");
                }
            }

            // Lưu order ID vào session
            $_SESSION['last_order_id'] = $orderId;

            // Xóa giỏ hàng
            unset($_SESSION['cart']);

            error_log("Order process completed successfully");

            // Chuyển hướng đến trang xác nhận
            header('Location: /T6-Sang/webbanhang/product/orderConfirmation');
            exit();
        } catch (Exception $e) {
            error_log("Error in processCheckout: " . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: /T6-Sang/webbanhang/product/checkout');
            exit();
        }
    }

    public function orderConfirmation($orderId)
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . ROOT_URL . '/Account/login');
                exit;
            }

            // Lấy thông tin đơn hàng
            $stmt = $this->db->prepare("
                SELECT * FROM orders WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$orderId, $_SESSION['user_id']]);
            $order = $stmt->fetch();

            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            // Lấy chi tiết đơn hàng
            $stmt = $this->db->prepare("
                SELECT od.*, p.name as product_name
                FROM order_details od
                JOIN products p ON od.product_id = p.id
                WHERE od.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $order['details'] = $stmt->fetchAll();

            // Hiển thị trang xác nhận
            require_once ROOT_PATH . '/app/views/shares/header.php';
            require_once ROOT_PATH . '/app/views/product/orderConfirmation.php';
            require_once ROOT_PATH . '/app/views/shares/footer.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/Product/list');
            exit;
        }
    }

    public function orderSuccess($orderId = null)
    {
        if (!$orderId) {
            header('Location: /T6-Sang/webbanhang/product');
            exit;
        }

        try {
            // Lấy thông tin đơn hàng
            $stmt = $this->db->prepare("
                SELECT o.*, od.quantity, od.price, p.name as product_name
                FROM orders o
                JOIN order_details od ON o.id = od.order_id
                JOIN products p ON od.product_id = p.id
                WHERE o.id = ?
            ");
            $stmt->execute([$orderId]);
            $orderDetails = $stmt->fetchAll();

            if (empty($orderDetails)) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            require_once ROOT_PATH . '/app/views/product/orderSuccess.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Đã có lỗi xảy ra: ' . $e->getMessage();
            header('Location: /T6-Sang/webbanhang/product');
            exit;
        }
    }

    public function detail($id = null)
    {
        // Cập nhật số lượng giỏ hàng trong session
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("
                SELECT SUM(quantity) as total
                FROM carts
                WHERE user_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch();
            $_SESSION['cart_count'] = $result['total'] ?? 0;
        }

        try {
            // Debug để xem giá trị id nhận được
            error_log("Product ID received: " . print_r($id, true));

            // Kiểm tra ID sản phẩm
            if (!$id || !is_numeric($id)) {
                throw new Exception('ID sản phẩm không hợp lệ');
            }

            // Lấy thông tin sản phẩm
            $stmt = $this->db->prepare("
                SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new Exception('Không tìm thấy sản phẩm');
            }

            // Lấy sản phẩm liên quan
            $stmt = $this->db->prepare("
                SELECT * FROM products 
                WHERE category_id = ? AND id != ? 
                LIMIT 4
            ");
            $stmt->execute([$product['category_id'], $id]);
            $relatedProducts = $stmt->fetchAll();

            // Debug data trước khi render
            error_log("Product data: " . print_r($product, true));

            require_once ROOT_PATH . '/app/views/shares/header.php';
            require_once ROOT_PATH . '/app/views/product/detail.php';  // Sửa lại đường dẫn này
            require_once ROOT_PATH . '/app/views/shares/footer.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/Product/list');
            exit;
        }
    }

    public function buynow($id = null)
    {
        try {
            if ($id === null) {
                throw new Exception("Không tìm thấy ID sản phẩm");
            }

            $product = $this->productModel->getProductById($id);
            if (!$product) {
                throw new Exception("Không tìm thấy sản phẩm");
            }

            // Lấy số lượng từ form
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            // Đảm bảo số lượng tối thiểu là 1
            $quantity = max(1, $quantity);

            // Khởi tạo giỏ hàng nếu chưa có
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Thêm hoặc cập nhật sản phẩm vào giỏ hàng
            $_SESSION['cart'][$id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];

            // Chuyển thẳng đến trang thanh toán
            header('Location: /T6-Sang/webbanhang/product/checkout');
            exit;
        } catch (Exception $e) {
            die("Lỗi: " . $e->getMessage());
        }
    }

    public function getCartCount()
    {
        $count = 0;
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $count += $item['quantity'];
            }
        }
        echo $count;
        exit;
    }

    public function list()
    {
        // Cập nhật số lượng giỏ hàng trong session
        if (isset($_SESSION['user_id'])) {
            $stmt = $this->db->prepare("
                SELECT SUM(quantity) as total
                FROM carts
                WHERE user_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch();
            $_SESSION['cart_count'] = $result['total'] ?? 0;
        }

        try {
            // Lấy danh sách danh mục
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY name");
            $categories = $stmt->fetchAll();

            // Xây dựng câu truy vấn cơ bản
            $query = "
                SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1
            ";
            $params = [];

            // Thêm điều kiện tìm kiếm
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $query .= " AND p.name LIKE ?";
                $params[] = "%" . $_GET['search'] . "%";
            }

            // Thêm điều kiện lọc theo danh mục
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $query .= " AND p.category_id = ?";
                $params[] = $_GET['category'];
            }

            // Thêm sắp xếp
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'price_asc':
                        $query .= " ORDER BY p.price ASC";
                        break;
                    case 'price_desc':
                        $query .= " ORDER BY p.price DESC";
                        break;
                    default:
                        $query .= " ORDER BY p.id DESC";
                }
            } else {
                $query .= " ORDER BY p.id DESC";
            }

            // Thực thi truy vấn
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $products = $stmt->fetchAll();

            // Debug data
            error_log("Products data: " . print_r($products, true));
            error_log("Categories data: " . print_r($categories, true));

            // Truyền dữ liệu sang view
            $data = [
                'products' => $products,
                'categories' => $categories
            ];

            require_once ROOT_PATH . '/app/views/shares/header.php';
            require_once ROOT_PATH . '/app/views/user/product/list.php';  // Sửa lại đường dẫn này
            require_once ROOT_PATH . '/app/views/shares/footer.php';
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . ROOT_URL . '/Product/list');
            exit;
        }
    }

    public function placeOrder()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Vui lòng đăng nhập để đặt hàng');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ');
            }

            // Lấy thông tin từ form
            $name = $_POST['name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $note = $_POST['note'] ?? '';
            $userId = $_SESSION['user_id'];

            if (empty($name) || empty($phone) || empty($address)) {
                throw new Exception('Vui lòng điền đầy đủ thông tin giao hàng');
            }

            // Bắt đầu transaction
            $this->db->beginTransaction();

            try {
                // 1. Tạo đơn hàng mới
                $sql = "INSERT INTO orders (user_id, name, phone, address, note, total_amount, status) 
                       VALUES (?, ?, ?, ?, ?, 0, 0)";

                $stmt = $this->db->prepare($sql);
                $stmt->execute([$userId, $name, $phone, $address, $note]);
                $orderId = $this->db->lastInsertId();

                // 2. Lấy sản phẩm từ giỏ hàng
                $stmt = $this->db->prepare("
                    SELECT c.*, p.price, p.name as product_name
                    FROM carts c
                    JOIN products p ON c.product_id = p.id
                    WHERE c.user_id = ?
                ");
                $stmt->execute([$userId]);
                $cartItems = $stmt->fetchAll();

                if (empty($cartItems)) {
                    throw new Exception('Giỏ hàng trống');
                }

                // 3. Thêm chi tiết đơn hàng
                $stmt = $this->db->prepare("
                    INSERT INTO order_details (order_id, product_id, quantity, price)
                    VALUES (?, ?, ?, ?)
                ");

                $totalAmount = 0;
                foreach ($cartItems as $item) {
                    $stmt->execute([
                        $orderId,
                        $item['product_id'],
                        $item['quantity'],
                        $item['price']
                    ]);
                    $totalAmount += $item['price'] * $item['quantity'];
                }

                // 4. Cập nhật tổng tiền đơn hàng
                $stmt = $this->db->prepare("
                    UPDATE orders 
                    SET total_amount = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$totalAmount, $orderId]);

                // 5. Xóa giỏ hàng
                $stmt = $this->db->prepare("DELETE FROM carts WHERE user_id = ?");
                $stmt->execute([$userId]);

                // Commit transaction
                $this->db->commit();

                // Trả về JSON response
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Đặt hàng thành công',
                    'order' => [
                        'id' => $orderId,
                        'total_amount' => $totalAmount,
                        'address' => $address
                    ]
                ]);
                exit;
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception('Lỗi khi xử lý đơn hàng: ' . $e->getMessage());
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}
