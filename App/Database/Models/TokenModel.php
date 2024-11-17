<?php

namespace App\Database\Models;

use App\Core\Application;
use App\DataTransferObjects\Token\CreateNewTokenDto;
use App\Exceptions\System\DatabaseQueryException;

class TokenModel
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
}