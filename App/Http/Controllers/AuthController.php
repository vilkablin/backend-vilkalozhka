<?php

namespace App\Http\Controllers;

use App\Database\Models\AccessTokenModel;
use App\Database\Models\UserModel;
use App\DataTransferObjects\Token\CreateNewTokenDto;
use App\DataTransferObjects\User\CreateNewUserDto;
use App\Exceptions\User\UserNotFoundException;
use App\Http\Router\Request;
use Throwable;

final class AuthController extends BaseController
{
    /**
     * @param Request $request
     */
    public function logout(Request $request): void
    {
        $headers = $request->getAllHeaders();

        if (!isset($headers['Authorization'])) {
            $this->failedResponse('Токен не был передан', 422);
        }

        $token = $headers['Authorization'];

        $accessToken = new AccessTokenModel();

        try {
            if ($accessToken->deleteByToken($token)) {
                $this->successResponse([], 204);
            }

            $this->failedResponse('Ошибка удаления токена', 400);
        } catch (Throwable $e) {
            $this->failedResponse($e->getMessage());
        }
    }

    public function signin(Request $request): void
    {
        $data = $request->getBody();

        if (empty($data)) {
            $this->failedResponse('Ошибка валидации', 422);
        }

        if (!isset($data['username'], $data['password'])) {
            $this->failedResponse('Все поля должны быть заполнены', 422);
        }

        $model = new UserModel();

        try {
            $user = $model->getByUsername($data['username']);

            $token = hash('sha256', $user->userId . $user->email . time());

            (new AccessTokenModel())->create(new CreateNewTokenDto(
                userId: $user->userId,
                token: $token
            ));

            $this->successResponse(['token' => $token]);
        } catch (UserNotFoundException) {
            $this->failedResponse('Пользователь не существует', 404);
        } catch (Throwable $e) {
            $this->failedResponse($e->getMessage());
        }
    }

    public function signup(Request $request): void
    {
        $fields = [
            'username' => 'имя пользователя',
            'email' => 'электронная почта',
            'password' => 'пароль',
            'confirmed_password' => 'повторите пароль',
        ];

        $data = $request->getBody();

        if (empty($data)) {
            $this->failedResponse('Ошибка валидации', 422);
        }

        if (!isset($data['username'], $data['email'], $data['password'], $data['confirmed_password'])) {
            $this->failedResponse('Все поля должны быть заполнены', 422);
        }

        foreach ($data as $key => $value) {
            if (!empty(trim($value))) {
                continue;
            }

            $this->failedResponse(sprintf('Поле %s не может быть пустым!', $fields[$key] ?? $key), 422);
        }

        if ($data['confirmed_password'] !== $data['password']) {
            $this->failedResponse('Пароли не совпадают', 422);
        }

        $model = new UserModel();

        try {
            $model->getUserByUsernameOrEmail(
                username: (string)$data['username'],
                email: (string)$data['email']
            );

            $this->failedResponse('Пользователь с такой электронной почтой или именем пользователя уже существует', 409);
        } catch (UserNotFoundException) {
        } catch (Throwable $e) {
            $this->failedResponse($e->getMessage());
        }

        // Если проверки пройдены, создаем пользователя
        try {
            $user = $model->create(new CreateNewUserDto(
                username: $data['username'],
                email: $data['email'],
                password: password_hash($data['password'], PASSWORD_BCRYPT),
                about: null,
                photo: null,
            ));

            $token = hash('sha256', $user->userId . $user->email . time());

            (new AccessTokenModel())->create(new CreateNewTokenDto(
                userId: $user->userId,
                token: $token
            ));

            $this->successResponse(['token' => $token]);
        } catch (Throwable $e) {
            $this->failedResponse('Ошибка при создании пользователя: ' . $e->getMessage());
        }
    }
}