<?php

namespace App\Domain\Entity;

class TokenEntity
{
    public function __construct(
        public int    $tokenId,
        public string $token,
        public int    $userId,
        public int    $createdAt,
    )
    {
    }
}