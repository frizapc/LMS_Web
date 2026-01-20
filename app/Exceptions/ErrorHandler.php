<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ErrorHandler
{
    public function handler(Throwable $exception, ?string $message = null)
    {
        if (request()->expectsJson()) { 
            return $this->jsonResponse($exception, $message);
        }

        return $this->viewResponse($exception, $message);
    }

    private function jsonResponse(Throwable $e, ?string $message = null)
    {
        $statusCode = $this->resolveStatusCode($e);

        return response()->json([
            'success' => false,
            'message' => $message ?: $this->defaultMessage($statusCode),
        ], $statusCode);
    }

    private function viewResponse(Throwable $e, ?string $message = null)
    {
        $statusCode = $this->resolveStatusCode($e);

        // Selalu gunakan 1 file error view kustom
        return response()->view('errors.custom', [
            'title'   => $this->defaultMessage($statusCode),
            'code'    => $statusCode,
            'message' => $message ?: $this->defaultMessage($statusCode),
        ], $statusCode);
    }

    /**
     * Tentukan status code berdasarkan tipe exception
     */
    private function resolveStatusCode(Throwable $e): int
    {

        if ($e instanceof HttpExceptionInterface) {
            return $e->getStatusCode();
        }

        if ($e instanceof AuthenticationException) {
            return 401;
        }

        if ($e instanceof ModelNotFoundException) {
            return 404;
        }

        if ($e instanceof ValidationException) {
            return 400;
        }

        return 500; // default
    }

    /**
     * Pesan bawaan bila exception tidak memiliki message
     */
    private function defaultMessage(int $status): string
    {
        return match ($status) {
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Page not found',
            419 => 'Page expired',
            422 => 'Unprocessable Entity',
            500 => 'Internal Server Error',
            default => 'Unexpected Error',
        };
    }
}
