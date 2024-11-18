<?php

namespace App\Exceptions\Tokens;

use Exception;

class AccessTokenNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Токен доступа не найден', 404);
    }
}