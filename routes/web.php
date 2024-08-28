<?php

use App\Http\Controllers\StorageController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__ . '/auth.php';

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'currentUser'])->name('user');
    Route::get('/user/media', [UserController::class, 'media'])->name('user.media');
    // getMediaCollectionSize
    Route::get('/user/media/{id}', [UserController::class, 'mediaSize'])->name('user.media.size');
    Route::get('/user/media/{media}', [UserController::class, 'showMedia'])->name('user.media.show');
    Route::delete('/user/media/{media}', [UserController::class, 'destroyMedia'])->name('user.media.destroy');

    Route::get('/upload', [UploadController::class, 'index'])->name('upload');
    Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
    Route::delete('/upload/{upload}', [UploadController::class, 'destroy'])->name('upload.destroy');

    Route::get('/storage/{disk}/{path}', StorageController::class)
        ->where('path', '.*')
        ->name('storage');
});
