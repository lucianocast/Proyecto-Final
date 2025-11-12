<?php

namespace App\Http\Controllers;

use App\Models\CategoriaProducto;
use Illuminate\Http\Request;

class CategoriaProductoController extends Controller
{
    public function index()
    {
        $categorias = CategoriaProducto::orderBy('nombre')->paginate(20);
        return view('encargado.categorias_productos.index', compact('categorias'));
    }

    public function create()
    {
        return view('encargado.categorias_productos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|unique:categoria_productos,nombre',
            'descripcion' => 'nullable|string',
        ]);

        $categoria = CategoriaProducto::create($data);

        // Si es una petición AJAX/JSON, devolver la categoría creada
        if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
            return response()->json(['categoria' => $categoria], 201);
        }

        return redirect()->route('encargado.categorias-productos.index')->with('status', 'Categoría creada');
    }

    public function edit(CategoriaProducto $categorias_producto)
    {
        return view('encargado.categorias_productos.edit', ['categoria' => $categorias_producto]);
    }

    public function update(Request $request, CategoriaProducto $categorias_producto)
    {
        $data = $request->validate([
            'nombre' => 'required|string|unique:categoria_productos,nombre,' . $categorias_producto->id,
            'descripcion' => 'nullable|string',
        ]);

        $categorias_producto->update($data);

        return redirect()->route('encargado.categorias-productos.index')->with('status', 'Categoría actualizada');
    }

    public function destroy(CategoriaProducto $categorias_producto)
    {
        if ($categorias_producto->productos()->exists()) {
            return redirect()->route('encargado.categorias-productos.index')->with('error', 'No se puede eliminar una categoría con productos asociados');
        }

        $categorias_producto->delete();

        return redirect()->route('encargado.categorias-productos.index')->with('status', 'Categoría eliminada');
    }
}
