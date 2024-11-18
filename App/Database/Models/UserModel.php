<?php

namespace App\Database\Models;

use App\DataTransferObjects\User\CreateNewUserDto;
use App\Domain\Entity\UserEntity;
use App\Exceptions\System\DatabaseQueryException;
use App\Exceptions\User\UserNotFoundException;

class UserModel extends BaseModel
{
    private string $table = 'users';

    /**
     * @throws UserNotFoundException
     * @throws DatabaseQueryException
     */
    public function getById(int $id): UserEntity
    {
        $result = $this->executeQuery(
            query: "SELECT * FROM $this->table WHERE id = :id",
            params: [':id' => $id],
            fetchSingle: true
        );

        if (!$result) {
            throw new UserNotFoundException();
        }

        return $this->mapToEntity($result);
    }

    /**
     * @throws UserNotFoundException
     * @throws DatabaseQueryException
     */
    public function getByEmail(string $email): UserEntity
    {
        $result = $this->executeQuery(
            query: "SELECT * FROM $this->table WHERE email = :email",
            params: [':email' => $email],
            fetchSingle: true
        );

        if (!$result) {
            throw new UserNotFoundException();
        }

        return $this->mapToEntity($result);
    }

    /**
     * @throws UserNotFoundException
     * @throws DatabaseQueryException
     */
    public function create(CreateNewUserDto $params): UserEntity
    {
        $userId = $this->insert($this->table, [
            'username' => $params->username,
            'email' => $params->email,
            'password' => $params->password,
            'about' => $params->about,
            'photo_path' => $params->photo,
        ]);

        return $this->getById($userId);
    }

    private function mapToEntity(array $data): UserEntity
    {
        return new UserEntity(
            userId: $data['id'],
            username: $data['username'],
            email: $data['email'],
            password: $data['password'],
            about: $data['about'],
            photoPath: $data['photo_path'],
        );
    }

    public function getTable()
    {
        return $this->table;
    }
}