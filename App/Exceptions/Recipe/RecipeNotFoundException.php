<?php

namespace App\Exceptions\Recipe;

use Exception;

class RecipeNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Recipe not found', 404);
    }
}