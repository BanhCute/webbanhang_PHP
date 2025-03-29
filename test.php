<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ROOT_PATH', __DIR__);
require_once 'app/config/database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Test kết nối và query trực tiếp
    $stmt = $conn->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'message' => 'Kết nối thành công',
        'total' => count($products),
        'data' => $products
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    error_log("Lỗi test.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
