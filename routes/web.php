<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ImageController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('hv')->group(function () {
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
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

    Route::get('/images', [ImageController::class, 'index'])->name('images.index');
    Route::patch('/images/{id}/update-name', [ImageController::class, 'updateName'])->name('images.updateName');
    Route::post('/api/images/store', [ImageController::class, 'store'])
        ->withoutMiddleware(ValidateCsrfToken::class);
    Route::get('/api/images/store', [ImageController::class, 'store'])
        ->withoutMiddleware(ValidateCsrfToken::class);


});
