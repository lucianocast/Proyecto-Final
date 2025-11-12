<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Insumo;
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
        return view('encargado.proveedores.create');
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
        return view('encargado.proveedores.edit', compact('proveedor'));
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
