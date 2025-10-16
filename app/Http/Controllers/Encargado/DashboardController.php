<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('encargado.dashboard');
    }

    public function compras()
    {
        return view('encargado.compras.index');
    }

    public function proveedores()
    {
        return view('encargado.proveedores.index');
    }

    public function produccion()
    {
        return view('encargado.produccion.index');
    }

    public function stock()
    {
        return view('encargado.stock.index');
    }
}
