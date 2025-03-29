<?php
header('Content-Type: application/json');
require_once 'app/config/database.php';

try {
    $stmt = $conn->query("SELECT * FROM products LIMIT 5");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        'status' => 'success',
        'data' => $products
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
