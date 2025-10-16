<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function usuariosRoles()
    {
        return view('admin.usuarios_roles');
    }

    public function sistema()
    {
        return view('admin.sistema');
    }

    public function notificaciones()
    {
        return view('admin.notificaciones');
    }

    public function reportes()
    {
        return view('admin.reportes');
    }

    public function auditoria()
    {
        return view('admin.auditoria');
    }
}
