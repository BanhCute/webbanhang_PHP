<?php
class CategoryController
{
    private $db;

    public function __construct()
    {
        // Sử dụng kết nối từ file config
        require_once 'app/config/database.php';
        global $conn;
        $this->db = $conn;
    }


    public function list()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM categories ORDER BY id DESC");
            $categories = $stmt->fetchAll();
            require_once 'app/views/category/list.php';
        } catch (PDOException $e) {
            die("Lỗi truy vấn: " . $e->getMessage());
        }
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);

            if (!empty($name)) {
                try {
                    $stmt = $this->db->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $id]);
                    $_SESSION['success'] = 'Cập nhật danh mục thành công';
                } catch (PDOException $e) {
                    $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                }
            }
            header('Location: /T6-Sang/webbanhang/Category/list');
            exit;
        }
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);

            if (!empty($name)) {
                try {
                    $stmt = $this->db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                    $stmt->execute([$name, $description]);
                    $_SESSION['success'] = 'Thêm danh mục thành công';
                } catch (PDOException $e) {
                    $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
                }
            }
            header('Location: /T6-Sang/webbanhang/Category/list');
            exit;
        }
        // Hiển thị form thêm mới
        require_once 'app/views/category/add.php';
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['success'] = 'Xóa danh mục thành công';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: /T6-Sang/webbanhang/Category/list');
        exit;
    }
}
