<?php

namespace App\Http\Controllers;

use App\Database\Models\CommentModel;
use App\Database\Models\LikeModel;
use App\Database\Models\RecipeModel;
use App\Http\Router\Request;
use Throwable;

final class UserController extends BaseController
{
    public function getUserInformation(Request $request)
    {
        $user = $this->validateAuthorizationToken($request);

        try {
            $commentsCount = (new CommentModel())->getCommentsCountByUserId($user->userId);
            $likesCount = (new LikeModel())->getLikesCountByUserId($user->userId);
            $recipeCount = (new RecipeModel())->getRecipesCountByUserId($user->userId);

            $response = [
                'user_id' => $user->userId,
                'username' => $user->username,
                'email' => $user->email,
                'about' => $user->about,
                'photo_url' => $user->photoPath,
                'statistics' => [
                    'likes' => $likesCount,
                    'comments' => $commentsCount,
                    'followers' => 0,
                    'following' => 0,
                    'recipes' => $recipeCount,
                ]
            ];

            $this->successResponse($response);
        } catch (Throwable $e) {

        }
    }

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