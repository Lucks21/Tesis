<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrincipalController;

Route::get('/', function () {
    return view('welcome');
});

//método index de PrincipalController
Route::get('/principal', [PrincipalController::class, 'index']);