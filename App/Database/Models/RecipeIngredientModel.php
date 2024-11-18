<?php

namespace App\Database\Models;

use App\Core\Application;
use App\Domain\Entity\IngredientEntity;
use App\Exceptions\System\DatabaseQueryException;
use PDO;

class RecipeIngredientModel
{
    private string $table = 'recipes';

    /**
     * @param int $recipeId
     * @return array
     * @throws DatabaseQueryException
     */
    public function getIngredientsByRecipeId(int $recipeId): array
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT * FROM recipe_ingredients WHERE recipe_ingredients.recipe_id = :recipe_id");

        $statement->bindValue(':recipe_id', $recipeId);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return [];
        }

        $mapped = [];

        foreach ($result as $item) {
            $mapped[] = new IngredientEntity(
                ingredientId: $item['id'],
                name: $item['name'],
                recipeId: $item['recipe_id'],
            );
        }

        return $mapped;
    }
}