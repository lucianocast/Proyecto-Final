<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    /**
     * Muestra el catálogo de productos visibles.
     */
    public function index(Request $request): View
    {
        // Obtener todas las categorías para los filtros
        $categorias = CategoriaProducto::all();

        // Query base de productos visibles
        $query = Producto::visibleEnCatalogo()->with('variantes');

        // Filtrar por categoría si se especifica
        if ($request->has('categoria') && $request->categoria) {
            $query->where('categoria_producto_id', $request->categoria);
        }

        // Obtener productos con paginación
        $productos = $query->paginate(12);

        return view('frontend.catalogo.index', compact('productos', 'categorias'));
    }

    /**
     * Muestra el detalle de un producto específico.
     */
    public function show(Producto $producto): View
    {
        // Cargar relaciones para evitar N+1
        $producto->load('variantes', 'categoria');

        return view('frontend.catalogo.show', compact('producto'));
    }
}
