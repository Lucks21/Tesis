<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusquedaSimpleController;

use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\AuthController;


Route::get('/', [PrincipalController::class, 'index']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/busqueda', function () {
    return view('busqueda');
})->name('busqueda');

Route::get('/dashboard', [PrincipalController::class, 'showDashboard'])->name('dashboard');
Route::get('/existencias', [BusquedaSimpleController::class, 'index'])->name('detalle_material.index');
Route::get('/buscar', [BusquedaSimpleController::class, 'buscar']);
Route::get('/resultados', [BusquedaSimpleController::class, 'buscar'])->name('resultados');
