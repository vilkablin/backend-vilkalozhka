<?php

namespace App\Http\Middleware;

use App\Http\Router\Request;

class CorsMiddleware
{
    public function handle(Request $request): void
    {
        if ($request->getMethod() === Request::OPTIONS) {
            header("Access-Control-Allow-Methods: *");
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: *");

            die();
        }
    }
}