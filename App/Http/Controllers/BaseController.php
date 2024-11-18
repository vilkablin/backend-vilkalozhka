<?php

namespace App\Http\Controllers;

use App\Http\Router\Request;

class BaseController
{
    protected function validateAuthorizationToken(Request $request)
    {

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