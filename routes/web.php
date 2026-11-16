<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/books');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])
        ->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');
});

Route::get('/books', [BookController::class, 'index'])
    ->name('books.index');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::resource('books', BookController::class)
        ->except(['index']);
});
