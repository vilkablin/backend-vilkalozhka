<?php

namespace App\Core;

use App\Http\Controllers\BaseController;

class ExceptionHandler
{
    private int $httpStatusCode;

    public function __construct(int $httpStatusCode = 500)
    {
        $this->httpStatusCode = $httpStatusCode;
    }

    public function register(): void
    {
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleError($errno, $errstr, $errfile, $errline): void
    {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public function handleException($exception): void
    {
        http_response_code($this->httpStatusCode);
        $this->renderJson($exception);
    }

    public function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            http_response_code($this->httpStatusCode);
            (new BaseController())->failedResponse($error['message'], $this->httpStatusCode);
        }
    }

    private function renderJson($exception): void
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
        ]);
        exit;
    }
}
