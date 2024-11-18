<?php

namespace App\Http\Controllers;

use App\Database\Models\AccessTokenModel;
use App\Database\Models\UserModel;
use App\Domain\Entity\UserEntity;
use App\Http\Router\Request;
use Throwable;

class BaseController
{
    protected function validateAuthorizationToken(Request $request): UserEntity
    {
        $headers = $request->getAllHeaders();

        if (!isset($headers['Authorization'])) {
            $this->failedResponse('Forbidden', 403);
        }

        $token = $headers['Authorization'];

        try {
            $accessToken = (new AccessTokenModel())->getByToken($token);

            return (new UserModel())->getById($accessToken->userId);
        } catch (Throwable $exception) {
            $this->failedResponse($exception->getMessage(), $exception->getCode());
        }
    }

    public function successResponse(array $data, int $statusCode = 200): void
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

    public function failedResponse(string $message, int $statusCode = 500, ?array $data = null): void
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