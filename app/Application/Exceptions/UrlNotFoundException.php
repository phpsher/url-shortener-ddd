<?php

namespace App\Application\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UrlNotFoundException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'error' => 'URL not found',
        ]);
    }
}
