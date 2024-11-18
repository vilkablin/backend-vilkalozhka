<?php

namespace App\Domain\Entity;

use App\Domain\Enums\RecipeComplexityEnum;

class RecipeEntity
{
    public function __construct(
        public int                  $recipeId,
        public string               $title,
        public string               $description,
        public int                  $cookingTimeInMinutes,
        public RecipeComplexityEnum $complexity,
        public int                  $numberOfServings,
        public ?string              $photoPath,
        public int                  $userId,
        public string               $username,
    )
    {
    }
}