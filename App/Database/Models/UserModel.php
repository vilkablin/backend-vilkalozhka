<?php

namespace App\Database\Models;

use App\Core\Application;
use App\DataTransferObjects\User\CreateNewUserDto;
use App\Domain\Entity\UserEntity;
use App\Exceptions\System\DatabaseQueryException;
use App\Exceptions\User\UserNotFoundException;
use PDO;

class UserModel
{
    /**
     * @throws DatabaseQueryException
     * @throws UserNotFoundException
     */
    public function getById(int $id): UserEntity
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT * FROM users WHERE id = :id");

        $statement->bindParam(":id", $id);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($result)) {
            throw new UserNotFoundException();
        }

        return new UserEntity(
            userId: $result["id"],
            username: $result["username"],
            email: $result["email"],
            password: $result["password"],
            about: $result["about"],
            photoPath: $result["photo_path"],
        );
    }

    /**
     * @throws UserNotFoundException
     * @throws DatabaseQueryException
     */
    public function getByEmail(string $email): UserEntity
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT * FROM users WHERE email = :email");

        $statement->bindParam(":email", $email);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($result)) {
            throw new UserNotFoundException();
        }

        return new UserEntity(
            userId: $result["id"],
            username: $result["username"],
            email: $result["email"],
            password: $result["password"],
            about: $result["about"],
            photoPath: $result["photo_path"],
        );
    }

    /**
     * @throws UserNotFoundException
     * @throws DatabaseQueryException
     */
    public function getByUsername(string $username): UserEntity
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT * FROM users WHERE username = :username");

        $statement->bindParam(":username", $username);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($result)) {
            throw new UserNotFoundException();
        }

        return new UserEntity(
            userId: $result["id"],
            username: $result["username"],
            email: $result["email"],
            password: $result["password"],
            about: $result["about"],
            photoPath: $result["photo_path"],
        );
    }

    /**
     * @throws UserNotFoundException
     * @throws DatabaseQueryException
     */
    public function getUserByUsernameOrEmail(string $username, string $email): UserEntity
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT * FROM `users` WHERE `email` = :email OR `username` = :username");

        $statement->bindParam(":email", $email);
        $statement->bindParam(":username", $username);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($result)) {
            throw new UserNotFoundException();
        }

        return new UserEntity(
            userId: $result["id"],
            username: $result["username"],
            email: $result["email"],
            password: $result["password"],
            about: $result["about"],
            photoPath: $result["photo_path"],
        );
    }

    /**
     * @param CreateNewUserDto $params
     * @return UserEntity
     * @throws DatabaseQueryException
     * @throws UserNotFoundException
     */
    public function create(CreateNewUserDto $params): UserEntity
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("INSERT INTO users (username, email, password, about, photo_path) VALUES (:username, :email, :password, :about, :photo_path)");

        $statement->bindParam(":username", $params->username);
        $statement->bindParam(":email", $params->email);
        $statement->bindParam(":password", $params->password);
        $statement->bindParam(":about", $params->about);
        $statement->bindParam(":photo_path", $params->photo);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        $userId = $database->lastInsertId();

        return $this->getById($userId);
    }
}