<?php

namespace App\Http\Controllers;

use App\Database\Models\RecipeCategoryModel;
use App\Database\Models\RecipeIngredientModel;
use App\Database\Models\RecipeModel;
use App\Database\Models\RecipeStepModel;
use App\Domain\Entity\CategoryEntity;
use App\Domain\Entity\IngredientEntity;
use App\Domain\Entity\StepEntity;
use App\Exceptions\Recipe\RecipeNotFoundException;
use App\Exceptions\System\DatabaseQueryException;
use App\Http\Router\Request;

final class RecipeController extends BaseController
{
    public function create(Request $request)
    {
        $required = [
            'title', 'description', 'cooking_time',
            'ingredients', 'number_of_servings', 'complexity',
            'photo', 'categories', 'steps'
        ];

        $this->validateAuthorizationToken($request);

        $data = $request->getBody();

        if (empty($data)) {
            $this->failedResponse('Ошибка валидации', 422);
        }

        $exceptions = [];

        foreach ($required as $key) {
            if (isset($data[$key])) {
                continue;
            }

            $exceptions[] = $key;
        }

        if (!empty($exceptions)) {
            $this->failedResponse('Обязательные поля не заполнены', 422, $exceptions);
        }

//        if (!isset($data['title'], $data['description'], $data['cooking_time'], $data['number_of_servings'], $data['complexity'], $data['photo'])) {
//            $this->failedResponse('', 422);
//        }

        $categories = []; // NEW MODEL
        $ingredients = []; // NEW MODEL
        $steps = []; // NEW MODEL

        $fields = [
            'title',
            'description',
            'cooking_time',
            'complexity',
            'number_of_servings',
            'photo_path'
        ];
    }

    public function update(Request $request)
    {
        $this->validateAuthorizationToken($request);

    }

    public function index(Request $request)
    {
        try {
            $params = $request->getBody();

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
                $mapped[] = [
                    'recipe_id' => $item->recipeId,
                    'title' => $item->title,
                    'description' => $item->description,
                    'cooking_time' => $item->cookingTimeInMinutes,
                    'complexity' => $item->complexity->getLabel(),
                    'number_of_servings' => $item->numberOfServings,
                    'photo_path' => $item->photoPath,
                    'user' => [
                        'user_id' => $item->userId,
                        'username' => $item->username
                    ],
                    'statistics' => [
                        'likes_count' => 0,
                        'comments_count' => 0,
                    ]
                ];
            }

            $response = [
                'has_next_page' => $hasNextPage,
                'current_page' => $currentPage,
                'items' => $mapped,
            ];

            $this->successResponse($response);
        } catch (DatabaseQueryException $e) {
            $this->failedResponse($e->getMessage());
        }
    }

    /**
     * @param Request $request
     */
    public function getRecipeByRecipeId(Request $request): void
    {
        $data = $request->getBody();

        if (empty($data)) {
            $this->failedResponse('Запись не найдена!', 404);
        }

        $recipeId = $data['id'];

        try {
            $recipe = (new RecipeModel())->getRecipeByRecipeId($recipeId);

            $steps = (new RecipeStepModel())->getStepsByRecipeId($recipeId);
            $categories = (new RecipeCategoryModel())->getCategoriesByRecipeId($recipeId);
            $ingredients = (new RecipeIngredientModel())->getIngredientsByRecipeId($recipeId);

            $response = [
                'recipe_id' => $recipe->recipeId,
                'title' => $recipe->title,
                'description' => $recipe->description,
                'photo_path' => $recipe->photoPath,
                'number_of_servings' => $recipe->numberOfServings,
                'complexity' => $recipe->complexity->getLabel(),
                'cooking_time_in_minutes' => $recipe->cookingTimeInMinutes,
                'steps' => array_values(array_map(function (StepEntity $step) {
                    return [
                        'order' => $step->position,
                        'title' => $step->title,
                        'description' => $step->description,
                        'photo_path' => $step->photoPath,
                    ];
                }, $steps)),
                'categories' => array_map(function (CategoryEntity $category) {
                    return [
                        'name' => $category->name,
                        'value' => $category->value,
                    ];
                }, $categories),
                'ingredients' => array_map(function (IngredientEntity $ingredientEntity) {
                    return $ingredientEntity->name;
                }, $ingredients),
            ];

            $this->successResponse($response);
        } catch (RecipeNotFoundException $e) {
            $this->failedResponse($e->getMessage(), 404);
        } catch (DatabaseQueryException $e) {
            $this->failedResponse($e->getMessage());
        }
    }
}