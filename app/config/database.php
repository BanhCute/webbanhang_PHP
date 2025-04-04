<?php
header('Content-Type: text/html; charset=utf-8');

$host = "localhost";
$dbname = "my_store"; // Sửa lại tên database cho đúng
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Debug
    error_log("Kết nối database thành công");
} catch (PDOException $e) {
    error_log("Lỗi kết nối database: " . $e->getMessage());
    die("Kết nối thất bại: " . $e->getMessage());
}

