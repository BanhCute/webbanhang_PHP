<?php
header('Content-Type: text/html; charset=utf-8');

try {
    $conn = new PDO(
        "mysql:host=localhost;dbname=my_store;charset=utf8mb4",
        "root",
        ""
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
