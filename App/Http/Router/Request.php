<?php

namespace App\Http\Router;

use JsonException;

class Request
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const OPTIONS = 'OPTIONS';

    public function getRoute(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';

        // Отсекаем все параметры в запросе
        $position = strpos($path, '?');

        // Если их не было, то просто вернем наш путь
        if ($position === false) {
            return $path;
        }

        // Вырезаем из нашей строки путь начиная с начала (0) до позиции символа "?"
        return substr($path, 0, $position);
    }

    public function getMethod(): string
    {
        // Возвращаем метод запроса, иначе по умолчанию GET запрос
        return strtoupper($_SERVER['REQUEST_METHOD']) ?? self::GET;
    }

    public function getAllHeaders(): array
    {
        // Возвращаем все заголовки запроса, иначе пустой массив
        return getallheaders() ?? [];
    }

    /**
     * @throws JsonException
     */
    public function getBody(): array
    {
        $mapped = [];

        if ($this->getMethod() === self::POST) {
            $mapped = array_merge($mapped, $_POST, json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR));
        }

        return array_merge($mapped, $_FILES);
    }

    public function getQueryParams(): array
    {
        return $_GET ?? [];
    }
}