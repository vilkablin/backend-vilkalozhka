<?php

namespace App\Http\Controllers;

use App\Database\Models\AccessTokenModel;
use App\Database\Models\UserModel;
use App\Domain\Entity\UserEntity;
use App\Exceptions\System\DatabaseQueryException;
use App\Exceptions\Tokens\AccessTokenNotFoundException;
use App\Exceptions\User\UserNotFoundException;
use App\Http\Router\Request;
use JetBrains\PhpStorm\NoReturn;

class BaseController
{
    /**
     * @throws AccessTokenNotFoundException
     * @throws UserNotFoundException
     * @throws DatabaseQueryException
     */
    protected function validateAuthorizationToken(Request $request): UserEntity
    {
        $headers = $request->getAllHeaders();

        if (!isset($headers['Authorization'])) {
            $this->failedResponse('Forbidden', 403);
        }

        $token = $headers['Authorization'];

        $accessToken = (new AccessTokenModel())->getByToken($token);

        return (new UserModel())->getById($accessToken->userId);
    }

    #[NoReturn] public function successResponse(array $data, int $statusCode = 200): void
    {
        $response = [
            'data' => $data,
            'success' => true,
            'message' => null
        ];

        header('Content-Type: application/json');

        http_response_code($statusCode);

        echo json_encode($response);

        die();
    }

    #[NoReturn] public function failedResponse(string $message, int $statusCode = 500, ?array $data = null): void
    {
        $response = [
            'data' => $data,
            'success' => false,
            'message' => $message
        ];

        header('Content-Type: application/json');

        http_response_code($statusCode);

        echo json_encode($response);

        die();
    }
}