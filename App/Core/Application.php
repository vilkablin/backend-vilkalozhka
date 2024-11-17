<?php

namespace App\Core;

use App\Database\Database;
use App\Http\Router\Request;
use App\Http\Router\Router;

final class Application
{
    private static ?Application $instance = null;
    private Database $database;
    private Router $router;

    private function __construct()
    {
        $this->database = new Database('root', '', 'vilka', 'localhost');

        $this->router = new Router(new Request());
    }

    public static function getInstance(): Application
    {
        if (is_null(self::$instance)) {
            self::$instance = new Application();
        }

        return self::$instance;
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
