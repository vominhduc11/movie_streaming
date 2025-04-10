<?php
// app/core/Router.php
namespace App\Core;

class Router
{
    private static $routes = [];

    // Thêm route
    public static function add($method, $path, $controller, $action, $middleware = null)
    {
        self::$routes[] = [
            'method' => $method,
            'path' => $path,
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    // Thêm route GET
    public static function get($path, $controller, $action, $middleware = null)
    {
        self::add('GET', $path, $controller, $action, $middleware);
    }

    // Thêm route POST
    public static function post($path, $controller, $action, $middleware = null)
    {
        self::add('POST', $path, $controller, $action, $middleware);
    }

    // Thêm route PUT
    public static function put($path, $controller, $action, $middleware = null)
    {
        self::add('PUT', $path, $controller, $action, $middleware);
    }

    // Thêm route DELETE
    public static function delete($path, $controller, $action, $middleware = null)
    {
        self::add('DELETE', $path, $controller, $action, $middleware);
    }

    // Thêm middleware cho route
    public static function middleware($middleware, $routes)
    {
        foreach ($routes as $route) {
            $method = $route[0];
            $path = $route[1];
            $controller = $route[2];
            $action = $route[3];

            self::add($method, $path, $controller, $action, $middleware);
        }
    }

    // Xử lý request
    public static function dispatch()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $uri);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = '#^' . str_replace('/', '\/', $route['path']) . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Bỏ phần tử đầu tiên (match đầy đủ)

                // Kiểm tra middleware
                if ($route['middleware'] !== null) {
                    $middlewareClass = 'App\\Middleware\\' . $route['middleware'];
                    $middleware = new $middlewareClass();
                    $middleware->handle();
                }

                $controllerClass = 'App\\Controllers\\' . $route['controller'];
                $controller = new $controllerClass();

                call_user_func_array([$controller, $route['action']], $matches);
                return;
            }
        }

        // Route không tồn tại
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
    }
}
