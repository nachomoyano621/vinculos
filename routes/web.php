<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OSocialController;
use App\Http\Controllers\PacienteController;
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
    Route::prefix('osocial')->group(function () {
        Route::get('/', [OSocialController::class, 'index'])->name('osocial.index');
        Route::get('/data', [OSocialController::class, 'indexData'])->name('osocial.data');
        Route::post('/store', [OSocialController::class, 'store'])->name('osocial.store');
        Route::put('/update/{id}', [OSocialController::class, 'update'])->name('osocial.update');
        Route::get('/show/{osocial}', [OSocialController::class, 'show'])->name('osocial.show');
        Route::delete('/destroy/{osocial}', [OSocialController::class, 'destroy'])->name('osocial.destroy');
        Route::get('/count', [OSocialController::class, 'count'])->name('osocial.count');
        Route::get('/osocial/all', [OSocialController::class, 'getAll'])->name('osocial.all');
    });
    Route::prefix('pacientes')->group(function () {
        Route::get('/', [PacienteController::class, 'index'])->name('pacientes.index');
        Route::get('/data', [PacienteController::class, 'indexData'])->name('pacientes.data');
        Route::post('/store', [PacienteController::class, 'store'])->name('pacientes.store');
        Route::put('/update/{id}', [PacienteController::class, 'update'])->name('pacientes.update');
        Route::get('/show/{paciente}', [PacienteController::class, 'show'])->name('pacientes.show');
        Route::delete('/destroy/{paciente}', [PacienteController::class, 'destroy'])->name('pacientes.destroy');
        Route::get('/count', [OSocialController::class, 'count'])->name('pacientes.count');
    });
});





