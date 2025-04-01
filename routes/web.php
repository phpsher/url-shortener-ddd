<?php

use Illuminate\Support\Facades\Route;

Route::get('/{alias}', [App\Http\Controllers\Api\V1\UrlController::class, 'show'])->where('alias', '[a-zA-Z0-9]{4}');
