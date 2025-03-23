<?php
class CategoryModel
{
    private $conn;
    private $table = "categories";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllCategories()
    {
        try {
            $sql = "SELECT * FROM categories ORDER BY name";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die("Lỗi truy vấn: " . $e->getMessage());
        }
    }

    public function getCategoryById($id)
    {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Lỗi truy vấn: " . $e->getMessage());
        }
    }

    public function addCategory($name, $description)
    {
        try {
            $query = "INSERT INTO " . $this->table . " (name, description) VALUES (:name, :description)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":description", $description);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Lỗi thêm danh mục: " . $e->getMessage());
        }
    }

    public function updateCategory($id, $name, $description)
    {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET name = :name, description = :description 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":description", $description);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Lỗi cập nhật danh mục: " . $e->getMessage());
        }
    }

    public function deleteCategory($id)
    {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Lỗi xóa danh mục: " . $e->getMessage());
        }
    }
}
