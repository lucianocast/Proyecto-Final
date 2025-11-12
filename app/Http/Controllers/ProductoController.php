<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::activos()->with('categoria')->paginate(20);
        return view('encargado.produccion.index', compact('productos'));
    }

    public function create()
    {
        $categorias = CategoriaProducto::orderBy('nombre')->get();
        return view('encargado.produccion.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|unique:productos,nombre',
            'descripcion' => 'nullable|string',
            'categoria_producto_id' => 'required|exists:categoria_productos,id',
            'precio_venta' => 'required|numeric|min:0',
        ]);

        $data['activo'] = true;

        $producto = Producto::create($data);

        // Crear receta vacía asociada
        $producto->receta()->create(['nombre' => 'Receta para ' . $producto->nombre]);

        return redirect()->route('encargado.produccion')->with('status', 'Producto creado');
    }

    public function edit(Producto $producto)
    {
        $categorias = CategoriaProducto::orderBy('nombre')->get();
        return view('encargado.produccion.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $data = $request->validate([
            'nombre' => 'required|string|unique:productos,nombre,' . $producto->id,
            'descripcion' => 'nullable|string',
            'categoria_producto_id' => 'required|exists:categoria_productos,id',
            'precio_venta' => 'required|numeric|min:0',
        ]);

        $producto->update($data);

        return redirect()->route('encargado.produccion')->with('status', 'Producto actualizado');
    }

    public function destroy(Producto $producto)
    {
        $producto->activo = false;
        $producto->save();

        return redirect()->route('encargado.produccion')->with('status', 'Producto dado de baja');
    }
}
