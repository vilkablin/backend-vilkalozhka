<?php

namespace App\Exceptions\System;

use Exception;

class DatabaseQueryException extends Exception
{
    public function __construct()
    {
        parent::__construct("Ошибка выполнения запроса", 500);
    }
}