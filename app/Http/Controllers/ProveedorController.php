<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Insumo;
use App\Models\User;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::activos()->orderBy('nombre_empresa')->paginate(20);
        return view('encargado.proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        // Usuarios que no tienen proveedor asociado (posibles candidatos a ligar)
        $users = User::whereDoesntHave('proveedor')->orderBy('name')->get();

        return view('encargado.proveedores.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'cuit' => 'nullable|string|max:255|unique:proveedores,cuit',
            'nombre_contacto' => 'nullable|string|max:255',
            'email_pedidos' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id|unique:proveedores,user_id',
        ]);

        $data['activo'] = true;

    $proveedor = Proveedor::create($data);

        return redirect()->route('encargado.proveedores')->with('success', 'Proveedor creado');
    }

    public function show(Proveedor $proveedor)
    {
        $proveedor->load('insumos');

        // Insumos disponibles: los insumos que no están asociados a este proveedor
        $insumos_disponibles = Insumo::whereNotIn('id', $proveedor->insumos()->pluck('insumo_id'))->activos()->orderBy('nombre')->get();

        return view('encargado.proveedores.show', compact('proveedor', 'insumos_disponibles'));
    }

    public function edit(Proveedor $proveedor)
    {
        // Usuarios sin proveedor + el usuario actualmente asociado (si existe)
        $users = User::whereDoesntHave('proveedor')
            ->orWhere('id', $proveedor->user_id)
            ->orderBy('name')
            ->get();

        return view('encargado.proveedores.edit', compact('proveedor', 'users'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $data = $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'cuit' => 'nullable|string|max:255|unique:proveedores,cuit,' . $proveedor->id,
            'nombre_contacto' => 'nullable|string|max:255',
            'email_pedidos' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'notas' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id|unique:proveedores,user_id,' . $proveedor->id,
        ]);

        $proveedor->update($data);

        return redirect()->route('encargado.proveedores')->with('success', 'Proveedor actualizado');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->activo = false;
        $proveedor->save();

        return redirect()->route('encargado.proveedores')->with('success', 'Proveedor dado de baja');
    }
}
