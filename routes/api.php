<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::post('/store', [App\Http\Controllers\Api\V1\UrlController::class, 'store']);
});
