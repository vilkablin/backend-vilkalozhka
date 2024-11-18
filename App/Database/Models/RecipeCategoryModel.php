<?php

namespace App\Database\Models;

use App\Core\Application;
use App\Domain\Entity\CategoryEntity;
use App\Exceptions\System\DatabaseQueryException;
use PDO;

class RecipeCategoryModel
{
    private string $table = 'recipes';

    /**
     * @throws DatabaseQueryException
     */
    public function getCategoriesByRecipeId(int $recipeId): array
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT * FROM recipe_categories JOIN recipe_recipe_categories ON recipe_categories.id = recipe_recipe_categories.category_id WHERE recipe_recipe_categories.receipe_id = :recipe_id");

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
            $mapped[] = new CategoryEntity(
                categoryId: $item['category_id'],
                name: $item['name'],
                value: $item['value']
            );
        }

        return $mapped;
    }
}