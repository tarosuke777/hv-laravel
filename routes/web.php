<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('hv')->group(function () {
    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/v2', [VideoController::class, 'indexV2'])->name('videos.indexV2');
    Route::get('/api/videos/store', [VideoController::class, 'store']);
    Route::get('/api/videos/max-timestamp', [VideoController::class, 'fetchMaxTimestamp']);
});

