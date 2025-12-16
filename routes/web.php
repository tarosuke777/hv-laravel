<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('hv')->group(function () {
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/v2', [VideoController::class, 'indexV2'])->name('videos.indexV2');
    Route::post('/api/videos/store', [VideoController::class, 'store'])
        ->withoutMiddleware(ValidateCsrfToken::class);
    Route::get('/api/videos/store', [VideoController::class, 'store'])
        ->withoutMiddleware(ValidateCsrfToken::class);
    Route::get('/api/videos/max-timestamp', [VideoController::class, 'fetchMaxTimestamp']);
});

