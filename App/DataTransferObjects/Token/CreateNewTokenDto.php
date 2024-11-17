<?php

namespace App\DataTransferObjects\Token;

class CreateNewTokenDto
{
    public function __construct(
        public int    $userId,
        public string $token,
    )
    {
    }
}