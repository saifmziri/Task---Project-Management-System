<?php

namespace App\Http\Controllers\Concerns;

use App\Support\ApiResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait RespondsWithApi
{
    protected function ok(mixed $data = null, string $message = 'Success'): JsonResponse
    {
        return ApiResponse::success($data, $message, Response::HTTP_OK);
    }

    protected function created(mixed $data = null, string $message = 'Created successfully'): JsonResponse
    {
        return ApiResponse::success($data, $message, Response::HTTP_CREATED);
    }

    protected function fail(
        string $message = 'Something went wrong.',
        mixed $errors = null,
        int $status = Response::HTTP_BAD_REQUEST,
        ?string $code = null
    ): JsonResponse {
        return ApiResponse::error($message, $errors, $status, $code);
    }

    protected function paginated(LengthAwarePaginator $paginator, mixed $items, string $message = 'Success'): JsonResponse
    {
        return ApiResponse::paginated($paginator, $items, $message);
    }
}