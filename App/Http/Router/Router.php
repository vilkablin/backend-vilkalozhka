<?php

namespace App\Http\Router;

use App\Http\Controllers\BaseController;

class Router
{
    private Request $request;

    private array $routes = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get(string $path, callable|array $handler): void
    {
        $this->routes[Request::GET][$path] = $handler;
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->routes[Request::POST][$path] = $handler;
    }

    public function put(string $path, callable|array $handler): void
    {
        $this->routes[Request::PUT][$path] = $handler;
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->routes[Request::DELETE][$path] = $handler;
    }

    public function middleware(string $className): void
    {
        $middleware = new $className;

        $middleware->handle($this->request);
    }

    public function resolve(): void
    {
        $route = $this->request->getRoute();
        $method = $this->request->getMethod();

        $handler = $this->routes[$method][$route] ?? null;

        if (is_null($handler)) {
            (new BaseController())->failedResponse('Запрашиваемый вами ресурс не найден', 404);

            return;
        }

        if (is_callable($handler)) {
            echo call_user_func($handler, $this->request);

            return;
        }

        // Класс контроллера, метод который в контроллере = и все это лежит в переменной $handler
        [$class, $method] = $handler;

        // Создали новый класс контроллера, что использовать метод его
        $controller = new $class();

        // вывели результат метода класса, который создали
        echo $controller->$method($this->request);
    }
}