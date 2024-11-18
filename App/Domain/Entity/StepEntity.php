<?php

namespace App\Domain\Entity;

final class StepEntity
{
    public function __construct(
        public int     $stepId,
        public string  $title,
        public string  $description,
        public ?string $photoPath,
        public int     $position,
        public int     $recipeId,
    )
    {
    }
}