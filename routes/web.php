<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OSocialController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\ProfesionController;
use App\Http\Controllers\ProfesionalController;
use Illuminate\Support\Facades\Route;

// Autenticación
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


// CRUD Usuarios (protegido)
Route::middleware('auth')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/counts', [UserController::class, 'getCounts'])->name('counts');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');   
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
        Route::get('/osocial/all', [OSocialController::class, 'getAll'])->name('osocial.all');
    });
    Route::prefix('pacientes')->group(function () {
        Route::get('/', [PacienteController::class, 'index'])->name('pacientes.index');
        Route::get('/data', [PacienteController::class, 'indexData'])->name('pacientes.data');
        Route::post('/store', [PacienteController::class, 'store'])->name('pacientes.store');
        Route::put('/update/{id}', [PacienteController::class, 'update'])->name('pacientes.update');
        Route::get('/show/{paciente}', [PacienteController::class, 'show'])->name('pacientes.show');
        Route::delete('/destroy/{paciente}', [PacienteController::class, 'destroy'])->name('pacientes.destroy');
        Route::get('/{id}/notas', [PacienteController::class, 'verNotas'])->name('pacientes.notas');
        Route::get('/{id}/notas/data', [PacienteController::class, 'notasData'])->name('pacientes.notas.data');
    });   

    Route::prefix('notas')->group(function () {
        Route::get('/', [NotaController::class, 'index'])->name('notas.index');
        Route::post('/store', [NotaController::class, 'store'])->name('notas.store');
        Route::get('/{id}', [NotaController::class, 'show'])->name('notas.show');
        Route::put('/{id}', [NotaController::class, 'update'])->name('notas.update');
        Route::delete('/{id}', [NotaController::class, 'destroy'])->name('notas.destroy');
    });
    

Route::prefix('profesiones')->group(function () {
    Route::get('/', [ProfesionController::class, 'index'])->name('profesiones.index');
    Route::get('/data', [ProfesionController::class, 'indexData'])->name('profesiones.indexData'); // Ruta para DataTables
    Route::post('/', [ProfesionController::class, 'store'])->name('profesiones.store');
    Route::put('/{id}', [ProfesionController::class, 'update'])->name('profesiones.update');
    Route::get('/{id}', [ProfesionController::class, 'show'])->name('profesiones.show');
    Route::delete('/{id}', [ProfesionController::class, 'destroy'])->name('profesiones.destroy');  
});

Route::prefix('profesionales')->name('profesionales.')->group(function() {
    Route::get('/', [ProfesionalController::class, 'index'])->name('index');
    Route::get('/data', [ProfesionalController::class, 'indexData'])->name('indexData');
    Route::post('/store', [ProfesionalController::class, 'store'])->name('store');
    Route::get('/{profesional}', [ProfesionalController::class, 'show'])->name('show');
    Route::delete('/destroy/{profesional}', [ProfesionalController::class, 'destroy'])->name('destroy'); 
    Route::get('/profesiones/all', [ProfesionController::class, 'getAll'])->name('profesion.all'); 
});
});





