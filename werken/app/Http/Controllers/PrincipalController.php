<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class PrincipalController extends Controller
{
    public function index()
    {
        return view('PrincipalView');
    }
    public function showDashboard()
    {
        return view('dashboard');
    }
}
