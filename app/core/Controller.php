<?php
// app/core/Controller.php
namespace App\Core;

class Controller
{
    // Load model
    protected function model($model)
    {
        $modelClass = 'App\\Models\\' . $model;
        return new $modelClass();
    }

    // Load view
    protected function view($view, $data = [])
    {
        // Biến $data thành các biến có thể sử dụng trong view
        extract($data);

        // Kiểm tra file view tồn tại
        if (file_exists(VIEW_PATH . '/' . $view . '.php')) {
            require_once VIEW_PATH . '/' . $view . '.php';
        } else {
            die('View không tồn tại');
        }
    }

    // Kiểm tra phương thức request
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    // Lấy dữ liệu từ form POST
    protected function getPost($field = null, $defaultValue = null)
    {
        if ($field === null) {
            return $_POST;
        }

        return isset($_POST[$field]) ? $_POST[$field] : $defaultValue;
    }

    // Lấy dữ liệu từ GET
    protected function getQuery($field = null, $defaultValue = null)
    {
        if ($field === null) {
            return $_GET;
        }

        return isset($_GET[$field]) ? $_GET[$field] : $defaultValue;
    }

    // Chuyển hướng trang
    protected function redirect($url)
    {
        header('Location: ' . APP_URL . '/' . $url);
        exit;
    }

    // Kiểm tra đăng nhập
    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    // Kiểm tra đăng nhập Admin
    protected function isAdmin()
    {
        return isset($_SESSION['admin_id']);
    }

    // Trả về JSON
    protected function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}
