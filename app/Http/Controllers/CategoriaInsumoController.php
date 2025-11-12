<?php

namespace App\Http\Controllers;

use App\Models\CategoriaInsumo;
use Illuminate\Http\Request;

class CategoriaInsumoController extends Controller
{
    public function index()
    {
        $categorias = CategoriaInsumo::orderBy('nombre')->paginate(20);
        return view('categorias_insumos.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias_insumos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:categoria_insumos,nombre',
            'descripcion' => 'nullable|string',
        ]);

        $categoria = CategoriaInsumo::create($data);

        // Si la petición espera JSON (AJAX), devolver la categoría creada
        if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
            return response()->json(['categoria' => $categoria], 201);
        }

        return redirect()->route('encargado.categorias-insumos.index')->with('success', 'Categoría creada');
    }

    public function edit(CategoriaInsumo $categorias_insumo)
    {
        return view('categorias_insumos.edit', ['categoria' => $categorias_insumo]);
    }

    public function show(CategoriaInsumo $categorias_insumo)
    {
        return view('categorias_insumos.show', ['categoria' => $categorias_insumo]);
    }

    public function update(Request $request, CategoriaInsumo $categorias_insumo)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:categoria_insumos,nombre,' . $categorias_insumo->id,
            'descripcion' => 'nullable|string',
        ]);

    $categorias_insumo->update($data);

    return redirect()->route('encargado.categorias-insumos.index')->with('success', 'Categoría actualizada');
    }

    public function destroy(CategoriaInsumo $categorias_insumo)
    {
        if ($categorias_insumo->insumos()->exists()) {
            return redirect()->route('encargado.categorias-insumos.index')->with('error', 'No se puede eliminar una categoría que tiene insumos.');
        }

        $categorias_insumo->delete();

        return redirect()->route('encargado.categorias-insumos.index')->with('success', 'Categoría eliminada');
    }
}
