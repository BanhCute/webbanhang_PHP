<?php
class ProductModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getProducts()
    {
        try {
            $query = "SELECT p.*, c.name as category_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Lỗi truy vấn: " . $e->getMessage());
        }
    }

    public function getProductById($id)
    {
        try {
            $query = "SELECT p.*, c.name as category_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     WHERE p.id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);
            $result = $stmt->fetchAll();
            return $result ? $result[0] : null;
        } catch (PDOException $e) {
            throw new Exception("Lỗi lấy thông tin sản phẩm: " . $e->getMessage());
        }
    }

    public function addProduct($name, $description, $price, $category_id, $image)
    {
        try {
            // Xử lý upload ảnh
            if (is_array($image) && isset($image['name']) && !empty($image['name'])) {
                // Đường dẫn upload đúng
                $uploadDir = 'public/uploads/products/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Tạo tên file unique và giữ nguyên đuôi file
                $imageExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
                $imageFileName = uniqid() . '_' . time() . '.' . $imageExtension;
                $uploadFile = $uploadDir . $imageFileName;

                // Kiểm tra và chỉ cho phép upload ảnh
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($imageExtension, $allowedTypes)) {
                    throw new Exception('Chỉ cho phép upload file ảnh (JPG, JPEG, PNG, GIF)');
                }

                // Upload file
                if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
                    // Thêm sản phẩm với ảnh
                    $sql = "INSERT INTO products (name, description, price, category_id, image) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $this->conn->prepare($sql);
                    $result = $stmt->execute([$name, $description, $price, $category_id, $imageFileName]);

                    if (!$result) {
                        // Nếu thêm vào database thất bại, xóa file ảnh đã upload
                        if (file_exists($uploadFile)) {
                            unlink($uploadFile);
                        }
                        throw new Exception('Không thể thêm sản phẩm vào database');
                    }

                    return true;
                } else {
                    throw new Exception('Không thể upload file ảnh');
                }
            } else {
                // Thêm sản phẩm không có ảnh
                $sql = "INSERT INTO products (name, description, price, category_id) VALUES (?, ?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$name, $description, $price, $category_id]);
            }
        } catch (Exception $e) {
            error_log("Lỗi thêm sản phẩm: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateProduct($id, $name, $description, $price, $category_id, $image)
    {
        try {
            // Kiểm tra xem có file ảnh mới được upload không
            if (is_array($image) && isset($image['name']) && !empty($image['name'])) {
                // Xử lý upload ảnh mới
                $uploadDir = 'public/uploads/products/';
                $imageFileName = uniqid() . '_' . time() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
                $uploadFile = $uploadDir . $imageFileName;

                if (move_uploaded_file($image['tmp_name'], $uploadFile)) {
                    // Lấy tên ảnh cũ để xóa
                    $stmt = $this->conn->prepare("SELECT image FROM products WHERE id = ?");
                    $stmt->execute([$id]);
                    $oldImage = $stmt->fetchColumn();

                    // Nếu upload thành công, cập nhật với ảnh mới
                    $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?";
                    $stmt = $this->conn->prepare($sql);
                    $result = $stmt->execute([$name, $description, $price, $category_id, $imageFileName, $id]);

                    if ($result && $oldImage) {
                        // Xóa ảnh cũ
                        $oldImagePath = $uploadDir . $oldImage;
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    return $result;
                }
            } else {
                // Nếu không có ảnh mới, giữ nguyên ảnh cũ
                $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ? WHERE id = ?";
                $stmt = $this->conn->prepare($sql);
                return $stmt->execute([$name, $description, $price, $category_id, $id]);
            }
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật sản phẩm: " . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($id)
    {
        try {
            $sql = "DELETE FROM products WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            throw new Exception("Lỗi xóa sản phẩm: " . $e->getMessage());
        }
    }

    public function searchProducts($keyword)
    {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM product p 
                    LEFT JOIN category c ON p.category_id = c.id 
                    WHERE p.name LIKE :keyword 
                    OR p.description LIKE :keyword 
                    ORDER BY p.id DESC";

            $stmt = $this->conn->prepare($sql);
            $searchKeyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $searchKeyword, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi tìm kiếm: " . $e->getMessage());
            return [];
        }
    }

    public function getRelatedProducts($currentId, $categoryId, $limit = 4)
    {
        try {
            $query = "SELECT p.*, c.name as category_name 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.category_id = :category_id 
                      AND p.id != :current_id 
                      LIMIT :limit";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':current_id', $currentId);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi truy vấn: " . $e->getMessage());
        }
    }

    public function getAllProducts($filters = [])
    {
        try {
            $sql = "SELECT p.*, c.name as category_name 
                    FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE 1=1";
            $params = [];

            // Lọc theo danh mục
            if (!empty($filters['category'])) {
                $sql .= " AND p.category_id = ?";
                $params[] = $filters['category'];
            }

            // Lọc theo tên sản phẩm
            if (!empty($filters['search'])) {
                $sql .= " AND p.name LIKE ?";
                $params[] = "%{$filters['search']}%";
            }

            // Sắp xếp theo giá
            if (!empty($filters['sort'])) {
                switch ($filters['sort']) {
                    case 'price_asc':
                        $sql .= " ORDER BY p.price ASC";
                        break;
                    case 'price_desc':
                        $sql .= " ORDER BY p.price DESC";
                        break;
                    default:
                        $sql .= " ORDER BY p.id DESC";
                        break;
                }
            } else {
                // Mặc định sắp xếp theo ID giảm dần
                $sql .= " ORDER BY p.id DESC";
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllProducts: " . $e->getMessage());
            return [];
        }
    }
}
