<?php

namespace App\Domain\Entity;

class UserEntity
{
    public function __construct(
        public int     $userId,
        public string  $username,
        public string  $email,
        public string  $password,
        public ?string $about,
        public ?string $photoPath,
    )
    {
    }
}