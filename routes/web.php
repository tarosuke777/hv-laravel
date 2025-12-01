<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VideoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/videos/v2', [VideoController::class, 'indexV2'])->name('videos.indexV2');