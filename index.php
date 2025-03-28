<?php
session_start();
require_once 'app/config/config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding('UTF-8');

// Định nghĩa các hằng số
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
require_once 'app/controllers/AdminController.php';
require_once 'app/controllers/OrderController.php';  // Thêm dòng này

// Lấy URL và tách thành các phần
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$url_parts = explode('/', $url);

// Xác định controller và action
$controller = isset($url_parts[0]) ? $url_parts[0] : 'Product';  // Mặc định là Product
$action = isset($url_parts[1]) ? $url_parts[1] : 'list';        // Mặc định là list
$params = array_slice($url_parts, 2);

// Debug
error_log("URL: " . $url);
error_log("Controller: " . $controller);
error_log("Action: " . $action);

// Xử lý trang chủ
if (empty($url)) {
    require_once 'app/controllers/ProductController.php';
    $productController = new ProductController();
    $productController->list();  // Hiển thị danh sách sản phẩm làm trang chủ
    exit;
}

// Xử lý routing cho admin
if ($controller === 'admin') {
    require_once 'app/controllers/AdminController.php';
    $adminController = new AdminController();

    $adminAction = isset($url_parts[1]) ? $url_parts[1] : '';
    $adminSubAction = isset($url_parts[2]) ? $url_parts[2] : '';

    switch ($adminAction) {
        case 'order':
            if (empty($adminSubAction)) {
                $adminController->order();
            }
            break;

        case 'category':
            if (empty($adminSubAction)) {
                $adminController->category();
            } else {
                switch ($adminSubAction) {
                    case 'save':
                        $adminController->saveCategory();
                        break;
                    case 'update':
                        $adminController->updateCategory();
                        break;
                    case 'delete':
                        if (isset($url_parts[3])) {
                            $adminController->deleteCategory($url_parts[3]);
                        }
                        break;
                }
            }
            break;

        case 'product':
            if (empty($adminSubAction)) {
                $adminController->product();
            } else {
                switch ($adminSubAction) {
                    case 'add':
                        $adminController->add();
                        break;
                    case 'save':
                        $adminController->save();
                        break;
                    case 'edit':
                        if (isset($url_parts[3])) {
                            $adminController->edit($url_parts[3]);
                        }
                        break;
                    case 'update':
                        $adminController->update();
                        break;
                    case 'delete':
                        if (isset($url_parts[3])) {
                            $adminController->delete($url_parts[3]);
                        }
                        break;
                }
            }
            break;

        case 'user':
            if (empty($adminSubAction)) {
                $adminController->user();
            } else {
                switch ($adminSubAction) {
                    case 'save':
                        $adminController->saveUser();
                        break;
                    case 'update':
                        $adminController->updateUser();
                        break;
                    case 'delete':
                        if (isset($url_parts[3])) {
                            $adminController->deleteUser($url_parts[3]);
                        }
                        break;
                }
            }
            break;

        default:
            // Nếu không có action cụ thể, chuyển về trang quản lý sản phẩm
            $adminController->product();
            break;
    }
} else {
    // Xử lý các controller thông thường
    $controller_name = ucfirst($controller) . 'Controller';
    $controller_file = 'app/controllers/' . $controller_name . '.php';

    if (file_exists($controller_file)) {
        require_once $controller_file;
        $controller_instance = new $controller_name();

        if (method_exists($controller_instance, $action)) {
            call_user_func_array([$controller_instance, $action], $params);
        } else {
            // Chuyển về trang danh sách sản phẩm nếu không tìm thấy action
            header('Location: ' . ROOT_URL . '/Product/list');
            exit;
        }
    } else {
        // Chuyển về trang danh sách sản phẩm nếu không tìm thấy controller
        header('Location: ' . ROOT_URL . '/Product/list');
        exit;
    }
}
