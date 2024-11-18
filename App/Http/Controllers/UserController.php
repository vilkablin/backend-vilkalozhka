<?php

namespace App\Http\Controllers;

use App\Database\Models\CommentModel;
use App\Database\Models\LikeModel;
use App\Database\Models\RecipeModel;
use App\Http\Router\Request;
use Throwable;

final class UserController extends BaseController
{
    public function getUserInformation(Request $request): void
    {
        try {
            $user = $this->validateAuthorizationToken($request);

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
            $this->failedResponse($e->getMessage(), $e->getCode());
        }
    }

    public function getAllUsers(Request $request): void
    {
        try {
            $params = $request->getQueryParams();

            $currentPage = 1;

            if (isset($params['current_page'])) {
                $currentPage = (int)$params['current_page'];

                if ($currentPage < 1) {
                    $currentPage = 1;
                }
            }

            $limit = 8;

            if (isset($params['limit'])) {
                $limit = (int)$params['limit'];

                if ($limit < 1) {
                    $limit = 8;
                }
            }

            $offset = ($currentPage - 1) * $limit;

            $items = (new RecipeModel())->getWithPaginate($offset, $limit);

            $hasNextPage = false;

            if (count($items) > $limit) {
                array_pop($items);

                $hasNextPage = true;
            }

            $mapped = [];

            // TODO: add likes and comments
            foreach ($items as $item) {
                $mapped[] = [];
            }

            $responses = [
                'items' => [],
                'has_next_page' => $hasNextPage,
                'current_page' => $currentPage,
            ];

            $this->successResponse($responses);
        } catch (Throwable $e) {
            $this->failedResponse($e->getMessage(), $e->getCode());
        }
    }
}