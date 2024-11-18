<?php

namespace App\Domain\Enums;

enum RecipeComplexityEnum: string
{
    case EASY = 'easy';
    case MEDIUM = 'medium';
    case HARD = 'hard';

    public function getLabel(): string
    {
        return match ($this) {
            self::HARD => 'Сложно',
            self::MEDIUM => 'Среднее',
            self::EASY => 'Легкое'
        };
    }
}
