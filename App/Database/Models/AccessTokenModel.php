<?php

namespace App\Database\Models;

use App\Core\Application;
use App\DataTransferObjects\Token\CreateNewTokenDto;
use App\Domain\Entity\TokenEntity;
use App\Exceptions\System\DatabaseQueryException;
use App\Exceptions\Tokens\AccessTokenNotFoundException;
use PDO;

class AccessTokenModel
{
    private string $table = 'access_tokens';

    /**
     * @throws DatabaseQueryException
     */
    public function create(CreateNewTokenDto $params): int
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("INSERT INTO $this->table (user_id, token) VALUES (:user_id, :token)");

        $statement->bindParam(":user_id", $params->userId);
        $statement->bindParam(":token", $params->token);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        return $database->lastInsertId();
    }

    /**
     * @throws AccessTokenNotFoundException
     * @throws DatabaseQueryException
     */
    public function getByToken(string $token): TokenEntity
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT * FROM $this->table WHERE token = :token LIMIT 1");

        $statement->bindParam(":token", $token);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        $results = $statement->fetch(PDO::FETCH_ASSOC);

        if (empty($results)) {
            throw new AccessTokenNotFoundException();
        }

        return new TokenEntity(
            tokenId: $results['id'],
            token: $results['token'],
            userId: $results['user_id'],
            createdAt: $results['created_at'],
        );
    }

    /**
     * @throws DatabaseQueryException
     */
    public function deleteByToken(string $accessToken): bool
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("DELETE FROM $this->table WHERE token = :token");

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

        $statement = $database->prepare("SELECT EXISTS(SELECT 1 FROM $this->table WHERE token = :token) AS record_exists");

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

        $statement = $database->prepare("SELECT EXISTS(SELECT 1 FROM $this->table WHERE user_id = :user_id) AS record_exists");

        $statement->bindParam(":user_id", $userId, PDO::PARAM_INT);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        return (bool)$statement->fetchColumn();
    }

}