<?php
class ProductModel
{
    private $conn;
    private $table_name = "products"; // Đảm bảo tên bảng chính xác

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getProducts($page = 1, $limit = 5, $search = '', $category_id = null)
    {
        try {
            $where = [];
            $params = [];

            if (!empty($search)) {
                $where[] = "(p.name LIKE :search OR p.description LIKE :search)";
                $params[':search'] = "%$search%";
            }

            if ($category_id) {
                $where[] = "p.category_id = :category_id";
                $params[':category_id'] = $category_id;
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Đếm tổng số sản phẩm
            $countQuery = "SELECT COUNT(*) FROM " . $this->table_name . " p " . $whereClause;
            $stmt = $this->conn->prepare($countQuery);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $totalProducts = $stmt->fetchColumn();

            // Query chính
            $query = "SELECT p.id, p.name, p.description, p.price, c.name as category_name
                     FROM " . $this->table_name . " p
                     LEFT JOIN categories c ON p.category_id = c.id
                     $whereClause
                     ORDER BY p.id DESC
                     LIMIT :limit OFFSET :offset";

            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', ($page - 1) * $limit, PDO::PARAM_INT);
            $stmt->execute();

            return [
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $totalProducts,
                'page' => $page,
                'limit' => $limit,
                'totalPages' => ceil($totalProducts / $limit),
                'filters' => [
                    'search' => $search,
                    'category_id' => $category_id
                ]
            ];
        } catch (Exception $e) {
            error_log("Lỗi getProducts: " . $e->getMessage());
            throw $e;
        }
    }

    public function getProductById($id)
    {
        try {
            $query = "SELECT p.*, c.name as category_name 
                     FROM " . $this->table_name . " p
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE p.id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Lỗi getProductById: " . $e->getMessage());
            throw $e;
        }
    }

    public function addProduct($name, $description, $price, $category_id, $image = null)
    {
        try {
            // Validate
            $errors = [];
            if (empty($name)) {
                $errors['name'] = 'Tên sản phẩm không được để trống';
            }
            if (empty($description)) {
                $errors['description'] = 'Mô tả không được để trống';
            }
            if (!is_numeric($price) || $price < 0) {
                $errors['price'] = 'Giá sản phẩm không hợp lệ';
            }
            if (count($errors) > 0) {
                return $errors;
            }

            // Xử lý ảnh nếu có
            $image_name = '';
            if (!empty($image)) {
                $image_name = $image;
            }

            // Thêm sản phẩm
            $query = "INSERT INTO " . $this->table_name . " 
                     (name, description, price, category_id, image) 
                     VALUES (:name, :description, :price, :category_id, :image)";

            $stmt = $this->conn->prepare($query);

            // Bind các giá trị
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':image', $image_name);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Lỗi addProduct: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateProduct($id, $name, $description, $price, $category_id)
    {
        try {
            // Validate
            $errors = [];
            if (empty($name)) {
                $errors['name'] = 'Tên sản phẩm không được để trống';
            }
            if (empty($description)) {
                $errors['description'] = 'Mô tả không được để trống';
            }
            if (!is_numeric($price) || $price < 0) {
                $errors['price'] = 'Giá sản phẩm không hợp lệ';
            }
            if (count($errors) > 0) {
                return $errors;
            }

            // Cập nhật sản phẩm
            $query = "UPDATE " . $this->table_name . " 
                     SET name = :name, 
                         description = :description, 
                         price = :price, 
                         category_id = :category_id 
                     WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            // Làm sạch dữ liệu
            $name = htmlspecialchars(strip_tags($name));
            $description = htmlspecialchars(strip_tags($description));
            $price = htmlspecialchars(strip_tags($price));
            $category_id = htmlspecialchars(strip_tags($category_id));
            $id = htmlspecialchars(strip_tags($id));

            // Bind các giá trị
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category_id', $category_id);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Lỗi updateProduct: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProduct($id)
    {
        try {
            $sql = "DELETE FROM " . $this->table_name . " WHERE id = ?";
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
                    FROM " . $this->table_name . " p 
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
                      FROM " . $this->table_name . " p 
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
                    FROM " . $this->table_name . " p 
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

    public function getTotalProducts()
    {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM " . $this->table_name);
        return $stmt->fetchColumn();
    }
}
