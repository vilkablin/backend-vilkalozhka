<?php

namespace App\Database\Models;

use App\Core\Application;
use App\DataTransferObjects\Token\CreateNewTokenDto;
use App\Exceptions\System\DatabaseQueryException;
use PDO;

class AccessTokenModel
{
    /**
     * @throws DatabaseQueryException
     */
    public function create(CreateNewTokenDto $params): int
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("INSERT INTO access_tokens (user_id, token) VALUES (:user_id, :token)");

        $statement->bindParam(":user_id", $params->userId);
        $statement->bindParam(":token", $params->token);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        return $database->lastInsertId();
    }

    /**
     * @throws DatabaseQueryException
     */
    public function deleteByToken(string $accessToken): bool
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("DELETE FROM access_tokens WHERE token = :token");

        $statement->bindParam(":token", $accessToken);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        return !($statement->rowCount() === 0);
    }

    /**
     * Проверяет наличие записи по токену с использованием EXISTS.
     *
     * @param string $accessToken
     * @return bool
     * @throws DatabaseQueryException
     */
    public function existsByToken(string $accessToken): bool
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT EXISTS(SELECT 1 FROM access_tokens WHERE token = :token) AS record_exists");

        $statement->bindParam(":token", $accessToken);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        return (bool)$statement->fetchColumn();
    }

    /**
     * Проверяет наличие записи по user_id с использованием EXISTS.
     *
     * @param int $userId
     * @return bool
     * @throws DatabaseQueryException
     */
    public function existsByUserId(int $userId): bool
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT EXISTS(SELECT 1 FROM access_tokens WHERE user_id = :user_id) AS record_exists");

        $statement->bindParam(":user_id", $userId, PDO::PARAM_INT);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        return (bool)$statement->fetchColumn();
    }

}