<?php
require_once 'app/config/config.php';

// Start session
if (!isset($_SESSION)) {
    session_start();
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

// Kiểm tra xem ROOT_URL đã được định nghĩa chưa
if (!defined('ROOT_URL')) {
    define('ROOT_URL', '/T6-Sang/webbanhang');
}
define('ROOT_PATH', dirname(__FILE__));
define('APP_ROOT', ROOT_PATH . '/app');

// Load các file cần thiết
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/models/AccountModel.php';
require_once 'app/controllers/CategoryController.php';
require_once 'app/controllers/ProductController.php';
require_once 'app/controllers/AccountController.php';

// Phân tích URL
$urlParts = isset($_GET['url']) ? explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : [];

// Nếu không có controller nào được chỉ định, mặc định chuyển hướng đến ProductController
if (empty($urlParts)) {
    header('Location: ' . ROOT_URL . '/Product/list');
    exit;
}

$controllerName = ucfirst($urlParts[0]) . 'Controller';
$actionName = $urlParts[1] ?? 'index';

// Lấy các tham số còn lại
$params = array_slice($urlParts, 2);

$controllerFile = ROOT_PATH . '/app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();

    if (method_exists($controller, $actionName)) {
        call_user_func_array([$controller, $actionName], $params);
    } else {
        echo "Không tìm thấy action: " . $actionName;
    }
} else {
    // Nếu không tìm thấy controller, chuyển hướng đến trang sản phẩm
    header('Location: ' . ROOT_URL . '/Product/list');
    exit;
}
