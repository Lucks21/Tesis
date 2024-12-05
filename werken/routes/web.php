<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusquedaSimpleController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/existencias', [BusquedaSimpleController::class, 'index'])->name('detalle_material.index');
Route::get('/buscar-titulo', [BusquedaSimpleController::class, 'buscarPorTitulo'])->name('buscar.titulo');
Route::get('/buscar-autor', [BusquedaSimpleController::class, 'buscarPorAutor'])->name('buscar.autor');
Route::get('/buscar-materia', [BusquedaSimpleController::class, 'buscarPorMateria'])->name('buscar.materia');
Route::get('/buscar-editorial', [BusquedaSimpleController::class, 'buscarPorEditorial'])->name('buscar.editorial');
Route::get('/buscar-serie', [BusquedaSimpleController::class, 'buscarPorSerie'])->name('buscar.serie');
