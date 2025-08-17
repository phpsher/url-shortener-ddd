<?php

use App\Presentation\Http\Controllers\Api\V1\UrlController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1/urls')->group(function () {
    Route::get('/{alias}', [UrlController::class, 'show']);

    Route::post('/', [UrlController::class, 'save']);
});
