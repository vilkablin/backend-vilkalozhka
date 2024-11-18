<?php

namespace App\Domain\Entity;

class IngredientEntity
{
    public function __construct(
        public int    $ingredientId,
        public string $name,
        public int    $recipeId
    )
    {
    }
}