<?php
class OrderController
{
    private $db;

    public function __construct()
    {
        // Kết nối database
        require_once 'app/config/database.php';
        global $conn;
        $this->db = $conn;
    }

    public function history()
    {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: /T6-Sang/webbanhang/Account/login');
            exit();
        }

        try {
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

            // Load view
            include 'app/views/shares/header.php';
            include 'app/views/order/history.php';
            include 'app/views/shares/footer.php';
        } catch (Exception $e) {
            error_log("Order history error: " . $e->getMessage());
            header('Location: /T6-Sang/webbanhang/');
            exit();
        }
    }

    public function detail($id)
    {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header('Location: /T6-Sang/webbanhang/Account/login');
            exit();
        }

        try {
            // Lấy thông tin đơn hàng
            $stmt = $this->db->prepare("
                SELECT o.*, 
                       (SELECT COUNT(*) FROM order_details od WHERE od.order_id = o.id) as total_items,
                       (SELECT SUM(od.price * od.quantity) FROM order_details od WHERE od.order_id = o.id) as total_amount
                FROM orders o 
                WHERE o.id = ? AND o.user_id = ?
            ");
            $stmt->execute([$id, $_SESSION['user_id']]);
            $order = $stmt->fetch();

            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            // Lấy chi tiết đơn hàng
            $stmt = $this->db->prepare("
                SELECT od.*, p.name as product_name, p.image 
                FROM order_details od
                JOIN products p ON od.product_id = p.id
                WHERE od.order_id = ?
            ");
            $stmt->execute([$id]);
            $orderDetails = $stmt->fetchAll();

            // Load view
            include 'app/views/shares/header.php';
            include 'app/views/order/detail.php';
            include 'app/views/shares/footer.php';
        } catch (Exception $e) {
            error_log("Order detail error: " . $e->getMessage());
            header('Location: /T6-Sang/webbanhang/Order/history');
            exit();
        }
    }
}
