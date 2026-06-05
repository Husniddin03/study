<?php

use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about')->middleware('admin');

Route::get('/login', [UserAuthController::class, 'login'])->name('login');
Route::get('/register', [UserAuthController::class, 'register'])->name('register');

Route::post('/login', [UserAuthController::class, 'loginStore'])->name('login.store');
Route::post('/register', [UserAuthController::class, 'registerStore'])->name('register.store');

Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');

Route::get('/verify-email/{id}', [UserAuthController::class, 'verify'])
     ->name('email.verify')
     ->middleware('signed');