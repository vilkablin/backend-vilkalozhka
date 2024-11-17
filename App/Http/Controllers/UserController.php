<?php

namespace App\Http\Controllers;

final class UserController extends BaseController
{
    public function getAllUsers(): void
    {
        $this->successResponse([
            'users' => [
                [
                    'id' => 1,
                    'full_name' => 'John Doe',
                ],
                [
                    'id' => 2,
                    'full_name' => 'John Doe',
                ],
            ]
        ]);
    }
}