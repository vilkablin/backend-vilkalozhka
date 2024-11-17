<?php

namespace App\Database;

use App\Http\Controllers\BaseController;
use PDO;
use Throwable;

final class Database
{
    private string $username;
    private string $password;
    private string $database;
    private string $host;

    private PDO $connection;

    public function __construct(string $username, string $password, string $database, string $host)
    {
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->database = $database;

        $this->connect();
    }

    private function connect(): void
    {
        try {
            $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8;", $this->host, $this->database);
            $this->connection = new PDO($dsn, $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Throwable $e) {
            (new BaseController())->failedResponse('Database connection error: ' . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
