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

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// API Route Handler
if (isset($url[0]) && $url[0] === 'api') {
    try {
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
    } catch (Exception $e) {
        error_log("API Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// MVC Route Handler

$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';

$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

if (file_exists('app/controllers/' . $controllerName . '.php')) {
    require_once 'app/controllers/' . $controllerName . '.php';
    $controller = new $controllerName();
    if (method_exists($controller, $action)) {
        call_user_func_array([$controller, $action], array_slice($url, 2));
    } else {
        die('Action không tồn tại');
    }
} else {
    die('Controller không tồn tại');
}

error_log("URL: " . print_r($url, true));
error_log("Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Request Body: " . file_get_contents('php://input'));
