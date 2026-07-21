<?php

namespace App\Support;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK,
        ?array $meta = null
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'errors'  => null,
            'meta'    => $meta,
        ], $status);
    }

    public static function error(
        string $message = 'Something went wrong.',
        mixed $errors = null,
        int $status = Response::HTTP_BAD_REQUEST,
        ?string $code = null
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null,
            'errors'  => $errors,
            'meta'    => [
                'code' => $code,
            ],
        ], $status);
    }

    public static function paginated(
        LengthAwarePaginator $paginator,
        mixed $items,
        string $message = 'Success'
    ): JsonResponse {
        return self::success(
            data: $items,
            message: $message,
            status: Response::HTTP_OK,
            meta: [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page'    => $paginator->lastPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'from'         => $paginator->firstItem(),
                    'to'           => $paginator->lastItem(),
                    'has_more'     => $paginator->hasMorePages(),
                ],
            ]
        );
    }
}