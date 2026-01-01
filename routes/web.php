<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\VideoController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('hv')->group(function () {
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/v2', [VideoController::class, 'indexV2'])->name('videos.indexV2');
    Route::patch('/videos/{id}/update-name', [VideoController::class, 'updateName'])->name('videos.updateName');
    Route::post('/api/videos/store', [VideoController::class, 'store'])
        ->withoutMiddleware(ValidateCsrfToken::class);
    Route::get('/api/videos/store', [VideoController::class, 'store'])
        ->withoutMiddleware(ValidateCsrfToken::class);
    Route::get('/api/videos/max-timestamp', [VideoController::class, 'fetchMaxTimestamp']);

    Route::resource('books', BookController::class)->except(['store']);
    Route::post('/api/books/store', [BookController::class, 'store'])
        ->withoutMiddleware(ValidateCsrfToken::class);
    Route::get('/api/books/store', [BookController::class, 'store'])
        ->withoutMiddleware(ValidateCsrfToken::class);
});
