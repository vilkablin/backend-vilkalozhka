<?php

namespace App\Database\Models;

use App\Core\Application;
use App\Domain\Entity\RecipeEntity;
use App\Domain\Enums\RecipeComplexityEnum;
use App\Exceptions\Recipe\RecipeNotFoundException;
use App\Exceptions\System\DatabaseQueryException;
use PDO;

final class RecipeModel
{
    /**
     * @param int $offset
     * @param int $limit
     * @return array<RecipeEntity>
     * @throws DatabaseQueryException
     */
    public function getWithPaginate(int $offset, int $limit): array
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT r.id, r.title, r.description, r.cooking_time, r.complexity, r.number_of_servings, r.photo_path, r.user_id, u.username FROM recipes r JOIN users u ON u.id = r.user_id ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset");

        $newLimit = $limit + 1;

        $statement->bindParam(':limit', $newLimit, PDO::PARAM_INT);
        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return [];
        }

        $mapped = [];

        foreach ($result as $recipe) {
            $mapped[] = new RecipeEntity(
                recipeId: $recipe["id"],
                title: $recipe["title"],
                description: $recipe["description"],
                cookingTimeInMinutes: (int)$recipe["cooking_time"],
                complexity: RecipeComplexityEnum::from($recipe["complexity"]),
                numberOfServings: (int)$recipe["number_of_servings"],
                photoPath: $recipe["photo_path"] ?? null,
                userId: $recipe["user_id"],
                username: $recipe['username'],
            );
        }

        return $mapped;
    }

    /**
     * @throws DatabaseQueryException
     * @throws RecipeNotFoundException
     */
    public function getRecipeByRecipeId(int $recipeId): RecipeEntity
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT r.id, r.title, r.description, r.cooking_time, r.complexity, r.number_of_servings, r.photo_path, r.user_id, u.username FROM recipes r JOIN users u ON u.id = r.user_id WHERE r.id = :recipe_id");

        $statement->bindValue(':recipe_id', $recipeId);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            throw new RecipeNotFoundException();
        }

        return new RecipeEntity(
            recipeId: $result["id"],
            title: $result["title"],
            description: $result["description"],
            cookingTimeInMinutes: (int)$result["cooking_time"],
            complexity: RecipeComplexityEnum::from($result["complexity"]),
            numberOfServings: (int)$result["number_of_servings"],
            photoPath: $result["photo_path"] ?? null,
            userId: $result["user_id"],
            username: $result['username'],
        );
    }
}