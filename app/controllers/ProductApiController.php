<?php
class ProductApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        try {
            // Thêm error logging
            error_log("ProductApiController được khởi tạo");

            // Khởi tạo kết nối database
            global $conn;
            if (!isset($conn)) {
                require_once ROOT_PATH . '/app/config/database.php';
            }
            $this->db = $conn;

            // Khởi tạo ProductModel
            require_once ROOT_PATH . '/app/models/ProductModel.php';
            $this->productModel = new ProductModel($this->db);
        } catch (Exception $e) {
            error_log("Lỗi khởi tạo ProductApiController: " . $e->getMessage());
            throw $e;
        }
    }

    // GET /api/product
    public function index()
    {
        try {
            header('Content-Type: application/json; charset=utf-8');

            // Lấy các tham số từ query string
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            $search = $_GET['search'] ?? '';
            $category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

            $result = $this->productModel->getProducts($page, $limit, $search, $category_id);

            echo json_encode([
                'status' => 'success',
                'data' => $result['data'],
                'pagination' => [
                    'total' => $result['total'],
                    'page' => $result['page'],
                    'limit' => $result['limit'],
                    'totalPages' => $result['totalPages']
                ],
                'filters' => $result['filters']
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Lỗi API index: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // GET /api/product/{id}
    public function show($id)
    {
        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);

        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy sản phẩm']);
        }
    }

    // POST /api/product
    public function store()
    {
        try {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents("php://input"), true);

            // Debug
            error_log("Received data: " . print_r($data, true));

            // Validate dữ liệu
            $errors = [];
            if (empty($data['name'])) $errors['name'] = 'Tên sản phẩm không được trống';
            if (empty($data['description'])) $errors['description'] = 'Mô tả không được trống';
            if (!isset($data['price']) || !is_numeric($data['price'])) $errors['price'] = 'Giá không hợp lệ';
            if (empty($data['category_id'])) $errors['category_id'] = 'Danh mục không được trống';

            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $errors
                ]);
                return;
            }

            $result = $this->productModel->addProduct(
                $data['name'],
                $data['description'],
                $data['price'],
                $data['category_id']
            );

            if ($result === true) {
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Thêm sản phẩm thành công'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Thêm sản phẩm thất bại',
                    'errors' => $result
                ]);
            }
        } catch (Exception $e) {
            error_log("Lỗi store: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // PUT /api/product/{id}
    public function update($id)
    {
        try {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents("php://input"), true);

            // Debug
            error_log("Update data for ID $id: " . print_r($data, true));

            // Kiểm tra sản phẩm tồn tại
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Không tìm thấy sản phẩm'
                ]);
                return;
            }

            // Validate dữ liệu
            $errors = [];
            if (empty($data['name'])) $errors['name'] = 'Tên sản phẩm không được trống';
            if (empty($data['description'])) $errors['description'] = 'Mô tả không được trống';
            if (!isset($data['price']) || !is_numeric($data['price'])) $errors['price'] = 'Giá không hợp lệ';
            if (empty($data['category_id'])) $errors['category_id'] = 'Danh mục không được trống';

            if (!empty($errors)) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $errors
                ]);
                return;
            }

            $result = $this->productModel->updateProduct(
                $id,
                $data['name'],
                $data['description'],
                $data['price'],
                $data['category_id']
            );

            if ($result === true) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Cập nhật sản phẩm thành công'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Cập nhật sản phẩm thất bại',
                    'errors' => $result
                ]);
            }
        } catch (Exception $e) {
            error_log("Lỗi update: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // DELETE /api/product/{id}
    public function destroy($id)
    {
        try {
            header('Content-Type: application/json');

            // Kiểm tra sản phẩm tồn tại
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Không tìm thấy sản phẩm'
                ]);
                return;
            }

            $result = $this->productModel->deleteProduct($id);

            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Xóa sản phẩm thành công'
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Xóa sản phẩm thất bại'
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
