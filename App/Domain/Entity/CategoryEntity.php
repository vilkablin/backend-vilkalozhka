<?php

namespace App\Domain\Entity;

class CategoryEntity
{
    public function __construct(
        public int    $categoryId,
        public string $name,
        public string $value,
    )
    {
    }
}