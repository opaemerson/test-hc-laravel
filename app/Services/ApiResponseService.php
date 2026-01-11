<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;

class ApiResponseService
{
    public static function success(string $message, mixed $data = []): JsonResponse
    {
        return response()->json([
            'error'   => false,
            'message' => $message,
            'response'    => $data,
        ]);
    }

    public static function error(string $message, mixed $errors = []): JsonResponse
    {
        return response()->json([
            'error'   => true,
            'message' => $message,
            'response'  => $errors,
        ]);
    }
}
