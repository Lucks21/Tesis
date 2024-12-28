<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusquedaSimpleController;
use App\Http\Controllers\BusquedaAvanzadaController;
use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\AuthController;


Route::get('/', [PrincipalController::class, 'index']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [PrincipalController::class, 'showDashboard'])->name('dashboard');
Route::get('/existencias', [BusquedaSimpleController::class, 'index'])->name('detalle_material.index');
Route::get('/buscar-titulo', [BusquedaSimpleController::class, 'buscarPorTitulo'])->name('buscar.titulo');
Route::get('/buscar-autor', [BusquedaSimpleController::class, 'buscarPorAutor'])->name('buscar.autor');
Route::get('/buscar-materia', [BusquedaSimpleController::class, 'buscarPorMateria'])->name('buscar.materia');
Route::get('/buscar-editorial', [BusquedaSimpleController::class, 'buscarPorEditorial'])->name('buscar.editorial');
Route::get('/buscar-serie', [BusquedaSimpleController::class, 'buscarPorSerie'])->name('buscar.serie');

Route::get('/busqueda-avanzada', function () {return view('BusquedaAvanzada');})->name('busqueda-avanzada');
Route::get('/busqueda-avanzada/resultados', [BusquedaAvanzadaController::class, 'buscar'])->name('busqueda-avanzada-resultados');
