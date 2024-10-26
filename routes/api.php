<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\ShortenerController;

Route::get('/{shortUrl}', [ShortenerController::class, 'redirect']);

Route::prefix('/v1')->group(function(){
    Route::get('/shortens', [ShortenerController::class, 'all']);
    Route::post('/shorten', [ShortenerController::class, 'create']);
    Route::get('/shorten', [ShortenerController::class, 'find']);
    Route::put('/shorten', [ShortenerController::class, 'update']);
    Route::delete('/shorten', [ShortenerController::class, 'delete']);
    Route::get('/shorten/stats', [ShortenerController::class, 'stats']);
});
