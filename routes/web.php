<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Autenticación
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


// CRUD Usuarios (protegido)
Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('users/count', [UserController::class, 'count'])->name('users.count');
    Route::get('/users/data', [UserController::class, 'indexData'])->name('users.indexData');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');

    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');

});

