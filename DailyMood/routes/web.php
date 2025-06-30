<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MoodController;

Route::get('/', function () {
    return redirect()->route('moods.index');
});

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('moods', MoodController::class);

    Route::post('moods/{id}/restore', [MoodController::class, 'restore'])->name('moods.restore');

    Route::get('/moods/export/pdf', [MoodController::class, 'exportPdf'])->name('moods.export.pdf');
});
