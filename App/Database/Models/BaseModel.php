<?php

namespace App\Database\Models;

use App\Core\Application;
use App\Exceptions\System\DatabaseQueryException;
use PDO;

abstract class BaseModel
{
    protected PDO $connection;

    public function __construct()
    {
        $this->connection = Application::getInstance()->getDatabase()->getConnection();
    }

    /**
     * Выполняет запрос с параметрами и возвращает результаты.
     *
     * @param string $query SQL-запрос.
     * @param array $params Параметры для запроса.
     * @param bool $fetchSingle Если true, вернёт одну строку, иначе массив строк.
     * @return array|null Результаты запроса.
     * @throws DatabaseQueryException
     */
    protected function executeQuery(string $query, array $params = [], bool $fetchSingle = false): ?array
    {
        $statement = $this->connection->prepare($query);

        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }

        if (!$statement->execute()) {
            $errorInfo = $statement->errorInfo();
            throw new DatabaseQueryException("Database query failed: " . $errorInfo[2]);
        }

        return $fetchSingle ? $statement->fetch(PDO::FETCH_ASSOC) : $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Вставляет данные в таблицу и возвращает ID последней вставленной строки.
     *
     * @param string $table Название таблицы.
     * @param array $data Ассоциативный массив данных (ключи - столбцы, значения - значения).
     * @return int ID последней вставленной строки.
     * @throws DatabaseQueryException
     */
    protected function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));

        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $statement = $this->connection->prepare($query);

        foreach ($data as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        if (!$statement->execute()) {
            $errorInfo = $statement->errorInfo();
            throw new DatabaseQueryException("Insert query failed: " . $errorInfo[2]);
        }

        return (int)$this->connection->lastInsertId();
    }

    /**
     * Удаляет записи из таблицы по условию.
     *
     * @param string $table Название таблицы.
     * @param string $condition Условие удаления (например, "id = :id").
     * @param array $params Параметры для условия.
     * @throws DatabaseQueryException
     */
    protected function delete(string $table, string $condition, array $params): void
    {
        $query = "DELETE FROM $table WHERE $condition";
        $statement = $this->connection->prepare($query);

        foreach ($params as $key => $value) {
            $statement->bindValue($key, $value);
        }

        if (!$statement->execute()) {
            $errorInfo = $statement->errorInfo();
            throw new DatabaseQueryException("Delete query failed: " . $errorInfo[2]);
        }
    }

    /**
     * Обновляет записи в таблице по условию.
     *
     * @param string $table Название таблицы.
     * @param array $data Ассоциативный массив данных для обновления.
     * @param string $condition Условие обновления (например, "id = :id").
     * @param array $params Параметры для условия.
     * @throws DatabaseQueryException
     */
    protected function update(string $table, array $data, string $condition, array $params): void
    {
        $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $query = "UPDATE $table SET $setClause WHERE $condition";

        $statement = $this->connection->prepare($query);

        foreach ($data as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        foreach ($params as $key => $value) {
            $statement->bindValue(":$key", $value);
        }

        if (!$statement->execute()) {
            $errorInfo = $statement->errorInfo();
            throw new DatabaseQueryException("Update query failed: " . $errorInfo[2]);
        }
    }
}
