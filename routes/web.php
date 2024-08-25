<?php

use App\Http\Controllers\StorageController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__ . '/auth.php';

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn() => auth()->user())->name('user');

    Route::get('/upload', [UploadController::class, 'index']);
    Route::post('/upload', [UploadController::class, 'store']);
    Route::post('/upload/{upload}/pause', [UploadController::class, 'pause']);
    Route::delete('/upload/{upload}', [UploadController::class, 'destroy']);

    Route::get('/storage/{disk}/{path}', StorageController::class)
        ->where('path', '.*');
});
