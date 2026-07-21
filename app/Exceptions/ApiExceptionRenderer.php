<?php

namespace App\Exceptions;

use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ApiExceptionRenderer
{
    public static function render(Throwable $e)
    {
        
        return match (true) {
            $e instanceof ValidationException => ApiResponse::error('Validation failed.', $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY, 'VALIDATION_ERROR'),
            $e instanceof AuthenticationException => ApiResponse::error('Unauthenticated.', null, Response::HTTP_UNAUTHORIZED, 'UNAUTHENTICATED'),
            $e instanceof ModelNotFoundException => ApiResponse::error('Resource not found.', null, Response::HTTP_NOT_FOUND, 'NOT_FOUND'),
            $e instanceof ThrottleRequestsException => ApiResponse::error('Too many requests.', null, Response::HTTP_TOO_MANY_REQUESTS, 'RATE_LIMITED'),
            default => ApiResponse::error(app()->environment('production') ? 'Server error.' : $e->getMessage(), app()->environment('production') ? null : ['exception' => class_basename($e)], Response::HTTP_INTERNAL_SERVER_ERROR, 'SERVER_ERROR'),
        };
    }
}