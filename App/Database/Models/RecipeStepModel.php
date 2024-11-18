<?php

namespace App\Database\Models;

use App\Core\Application;
use App\Domain\Entity\StepEntity;
use App\Exceptions\System\DatabaseQueryException;
use PDO;

class RecipeStepModel
{
    /**
     * @throws DatabaseQueryException
     */
    public function getStepsByRecipeId(int $recipeId): array
    {
        $database = Application::getInstance()->getDatabase()->getConnection();

        $statement = $database->prepare("SELECT * FROM recipe_steps WHERE recipe_steps.recipe_id = :recipe_id ORDER BY position");

        $statement->bindValue(':recipe_id', $recipeId);

        if (!$statement->execute()) {
            throw new DatabaseQueryException();
        }

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($result)) {
            return [];
        }

        $mapped = [];

        foreach ($result as $step) {
            $mapped[$step['position']] = new StepEntity(
                stepId: $step['id'],
                title: $step['title'],
                description: $step['description'],
                photoPath: $step['photo_path'],
                position: $step['position'],
                recipeId: $step['recipe_id'],
            );
        }

        ksort($mapped);

        return $mapped;
    }
}