<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Chỉ định nghĩa ROOT_PATH và ROOT_URL nếu chưa được định nghĩa
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

// Không định nghĩa lại ROOT_URL vì đã có trong config.php
require_once 'app/config/config.php';
require_once 'app/config/database.php';

// Debug
error_log("Request URL: " . ($_GET['url'] ?? 'No URL'));

// Lấy URL từ request
$url = isset($_GET['url']) ? $_GET['url'] : '';
$url = rtrim($url, '/');

// Debug
error_log("Current URL: " . $url);
error_log("Session data: " . print_r($_SESSION, true));

/* Comment phần API để vô hiệu hóa
// API Route Handler
if (isset($url[0]) && $url[0] === 'api') {
    try {
        // Auth routes
        if (isset($url[1]) && $url[1] === 'auth') {
            require_once 'app/controllers/AuthApiController.php';
            $controller = new AuthApiController();
            
            switch ($url[2]) {
                case 'login':
                    $controller->login();
                    break;
                default:
                    http_response_code(404);
                    echo json_encode(['message' => 'Route not found']);
            }
            exit;
        }

        // Product routes
        if (isset($url[1]) && $url[1] === 'product') {
            require_once 'app/controllers/ProductApiController.php';
            $controller = new ProductApiController();
            
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    if (isset($url[2])) {
                        $controller->show($url[2]);
                    } else {
                        $controller->index();
                    }
                    break;
                case 'POST':
                    $controller->store();
                    break;
                case 'PUT':
                    if (isset($url[2])) {
                        $controller->update($url[2]);
                    }
                    break;
                case 'DELETE':
                    if (isset($url[2])) {
                        $controller->destroy($url[2]);
                    }
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['message' => 'Method not allowed']);
            }
            exit;
        }

        http_response_code(404);
        echo json_encode(['message' => 'API endpoint not found']);
        exit;

    } catch (Exception $e) {
        error_log("API Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}
*/

// Xử lý route
if (empty($url)) {
    // Trang chủ
    require_once 'app/controllers/ProductController.php';
    $controller = new ProductController();
    $controller->index();
} else {
    $parts = explode('/', $url);

    // Kiểm tra route admin
    if ($parts[0] === 'admin') {
        require_once 'app/controllers/AdminController.php';
        $controller = new AdminController();

        $adminAction = isset($parts[1]) ? $parts[1] : 'dashboard';
        $adminSubAction = isset($parts[2]) ? $parts[2] : '';
        $adminId = isset($parts[3]) ? $parts[3] : null;

        error_log("Admin action: $adminAction, Sub action: $adminSubAction, ID: $adminId");

        switch ($adminAction) {
            case 'dashboard':
                $controller->dashboard();
                break;

            case 'product':
                switch ($adminSubAction) {
                    case 'add':
                        $controller->add();
                        break;
                    case 'save':
                        $controller->save();
                        break;
                    case 'edit':
                        $controller->edit($adminId);
                        break;
                    case 'update':
                        $controller->update();
                        break;
                    case 'delete':
                        $controller->delete($adminId);
                        break;
                    default:
                        $controller->product();
                        break;
                }
                break;

            case 'category':
                switch ($adminSubAction) {
                    case 'save':
                        $controller->saveCategory();
                        break;
                    case 'update':
                        $controller->updateCategory();
                        break;
                    case 'delete':
                        $controller->deleteCategory($adminId);
                        break;
                    default:
                        $controller->category();
                        break;
                }
                break;

            case 'user':
                switch ($adminSubAction) {
                    case 'save':
                        $controller->saveUser();
                        break;
                    case 'update':
                        $controller->updateUser();
                        break;
                    case 'delete':
                        $controller->deleteUser($adminId);
                        break;
                    default:
                        $controller->user();
                        break;
                }
                break;

            case 'order':
                switch ($adminSubAction) {
                    case 'detail':
                        $controller->orderDetail($adminId);
                        break;
                    case 'update-status':
                        $controller->updateOrderStatus();
                        break;
                    default:
                        $controller->order();
                        break;
                }
                break;

            default:
                $controller->dashboard();
                break;
        }
        exit;
    } else {
        // Route thông thường
        $controllerName = ucfirst($parts[0]);
        $action = isset($parts[1]) ? $parts[1] : 'index';
        $param = isset($parts[2]) ? $parts[2] : null;

        $controllerFile = 'app/controllers/' . $controllerName . 'Controller.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerClass = $controllerName . 'Controller';
            $controller = new $controllerClass();

            if (method_exists($controller, $action)) {
                $controller->$action($param);
            } else {
                error_log("Method not found: $controllerClass::$action");
                header("HTTP/1.0 404 Not Found");
                require_once 'app/views/404.php';
            }
        } else {
            error_log("Controller not found: $controllerFile");
            header("HTTP/1.0 404 Not Found");
            require_once 'app/views/404.php';
        }
    }
}

error_log("URL: " . print_r($url, true));
error_log("Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Request Body: " . file_get_contents('php://input'));

// Đảm bảo các route sau có trong phần MVC handler
// admin/product/add
// admin/save
// admin/product/edit/{id}
// admin/update
// admin/product/delete/{id}
