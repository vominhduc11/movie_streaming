<?php
// app/core/App.php
namespace App\Core;

class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        // Kiểm tra controller
        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            if (file_exists(APP_PATH . '/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        // Kiểm tra action/method
        $controllerClass = 'App\\Controllers\\' . $this->controller;
        $this->controller = new $controllerClass();

        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Lấy parameters
        $this->params = $url ? array_values($url) : [];

        // Gọi controller và method với parameters
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    // Phân tích URL
    protected function parseUrl()
    {
        if (isset($_GET['url'])) {
            $url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
            return explode('/', $url);
        }
        return [];
    }
}
