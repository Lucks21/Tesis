<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BusquedaSimpleController;
use App\Http\Controllers\BusquedaAvanzadaController;
use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportacionController;


Route::get('/', [PrincipalController::class, 'index']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/busqueda', function () {
    return view('BusquedaView');
})->name('busqueda');

Route::get('/dashboard', [PrincipalController::class, 'showDashboard'])->name('dashboard');
Route::get('/buscar-titulo', [BusquedaSimpleController::class, 'buscarPorTitulo'])->name('buscar.titulo');
Route::get('/resultados', [BusquedaSimpleController::class, 'buscar'])->name('resultados');
Route::get('/recursos-asociados/{criterio}/{valor}', [BusquedaSimpleController::class, 'recursosAsociados'])
     ->name('recursos.asociados');

Route::get('/busqueda-avanzada', function () {return view('BusquedaAvanzada');})->name('busqueda-avanzada');
Route::get('/busqueda-avanzada/resultados', [BusquedaAvanzadaController::class, 'buscar'])->name('busqueda-avanzada-resultados');
Route::get('busqueda-avanzada/titulos/{autor}', [BusquedaAvanzadaController::class, 'mostrarTitulosPorAutor'])->name('mostrar-titulos-por-autor');
Route::get('busqueda-avanzada/titulos-editorial/{editorial}', [BusquedaAvanzadaController::class, 'mostrarTitulosPorEditorial'])->name('mostrar-titulos-por-editorial');
Route::get('busqueda-avanzada/titulos-materia/{materia}', [BusquedaAvanzadaController::class, 'mostrarTitulosPorMateria'])->name('mostrar-titulos-por-materia');
Route::get('busqueda-avanzada/titulos-serie/{serie}', [BusquedaAvanzadaController::class, 'mostrarTitulosPorSerie'])->name('mostrar-titulos-por-serie');
Route::get('/export-ris/{nroControl}', [ExportacionController::class, 'exportRIS'])->name('export.ris');
