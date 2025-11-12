<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\CategoriaInsumo;
use Illuminate\Http\Request;

class InsumoController extends Controller
{
    public function index()
    {
        $insumos = Insumo::activos()->with('categoria')->with('lotes')->paginate(20);
        return view('encargado.stock.index', compact('insumos'));
    }

    public function create()
    {
        $categorias = CategoriaInsumo::orderBy('nombre')->get();
        return view('insumos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:insumos,nombre',
            'descripcion' => 'nullable|string',
            'categoria_insumo_id' => 'required|exists:categoria_insumos,id',
            'unidad_de_medida' => 'required|string|max:50',
            'stock_minimo' => 'nullable|numeric|min:0',
            'ubicacion' => 'nullable|string|max:255',
        ]);

        $data['stock_minimo'] = $data['stock_minimo'] ?? 0;
        $data['activo'] = true;

    Insumo::create($data);

    return redirect()->route('encargado.insumos.index')->with('success', 'Insumo creado');
    }

    public function edit(Insumo $insumo)
    {
        $categorias = CategoriaInsumo::orderBy('nombre')->get();
        return view('insumos.edit', compact('insumo', 'categorias'));
    }

    public function show(Insumo $insumo)
    {
        $insumo->load('lotes');
        return view('insumos.show', compact('insumo'));
    }

    public function update(Request $request, Insumo $insumo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:insumos,nombre,' . $insumo->id,
            'descripcion' => 'nullable|string',
            'categoria_insumo_id' => 'required|exists:categoria_insumos,id',
            'unidad_de_medida' => 'required|string|max:50',
            'stock_minimo' => 'nullable|numeric|min:0',
            'ubicacion' => 'nullable|string|max:255',
        ]);

    $insumo->update($data);

    return redirect()->route('encargado.insumos.index')->with('success', 'Insumo actualizado');
    }

    public function destroy(Insumo $insumo)
    {
    $insumo->activo = false;
    $insumo->save();

    return redirect()->route('encargado.insumos.index')->with('success', 'Insumo dado de baja');
    }
}
