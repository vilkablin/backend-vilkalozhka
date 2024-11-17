<?php

namespace App\DataTransferObjects\User;

class CreateNewUserDto
{
    public function __construct(
        public string  $username,
        public string  $email,
        public string  $password,
        public ?string $about,
        public ?string $photo,
    )
    {
    }
}