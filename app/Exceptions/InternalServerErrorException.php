<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InternalServerErrorException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'error' => 'Internal Server Error',
        ], 500);
    }
}
