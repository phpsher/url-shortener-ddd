<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait Response
{
    public function successResponse($data, $message = '', $status = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $status);
    }

    public function errorResponse($error, $message = '', $status = 500): JsonResponse
    {
        return response()->json([
            'error' => $error,
            'message' => $message
        ], $status);
    }
}
