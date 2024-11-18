<?php

namespace App\Database\Models;

use App\Domain\Entity\RecipeEntity;
use App\Domain\Enums\RecipeComplexityEnum;
use App\Exceptions\Recipe\RecipeNotFoundException;
use App\Exceptions\System\DatabaseQueryException;

final class RecipeModel extends BaseModel
{
    private string $table = 'recipes';

    /**
     * @param int $offset
     * @param int $limit
     * @return array<RecipeEntity>
     * @throws DatabaseQueryException
     */
    public function getWithPaginate(int $offset, int $limit): array
    {
        $userTable = (new UserModel())->getTable();

        $results = $this->executeQuery(
            "SELECT r.id, r.title, r.description, r.cooking_time, r.complexity, r.number_of_servings, r.photo_path, r.user_id, u.username FROM $this->table r JOIN $userTable u ON u.id = r.user_id ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset",
            params: [
                ':limit' => $limit + 1,
                ':offset' => $offset
            ],
        );

        if (empty($results)) {
            return [];
        }

        return array_map(fn(array $item) => $this->mapToEntity($item), $results);
    }

    /**
     * @throws DatabaseQueryException
     * @throws RecipeNotFoundException
     */
    public function getRecipeByRecipeId(int $recipeId): RecipeEntity
    {
        $userTable = (new UserModel())->getTable();

        $results = $this->executeQuery(
            query: "SELECT r.id, r.title, r.description, r.cooking_time, r.complexity, r.number_of_servings, r.photo_path, r.user_id, u.username FROM $this->table r JOIN $userTable u ON u.id = r.user_id WHERE r.id = :recipe_id",
            params: [':recipe_id' => $recipeId],
            fetchSingle: true,
        );

        if (is_null($results)) {
            throw new RecipeNotFoundException();
        }

        return $this->mapToEntity($results);
    }

    public function getRecipesCountByUserId(int $userId)
    {
        $results = $this->executeQuery(
            query: "SELECT COUNT(*) as count FROM $this->table WHERE user_id = :user_id",
            params: [":user_id" => $userId],
            fetchSingle: true,
        );

        return $results["count"] ?? 0;
    }

    private function mapToEntity(array $data): RecipeEntity
    {
        return new RecipeEntity(
            recipeId: $data["id"],
            title: $data["title"],
            description: $data["description"],
            cookingTimeInMinutes: (int)$data["cooking_time"],
            complexity: RecipeComplexityEnum::from($data["complexity"]),
            numberOfServings: (int)$data["number_of_servings"],
            photoPath: $data["photo_path"] ?? null,
            userId: $data["user_id"],
            username: $data['username'],
        );
    }
}