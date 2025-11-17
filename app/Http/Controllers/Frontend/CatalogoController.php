<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\View\View;

class CatalogoController extends Controller
{
    /**
     * Muestra el catálogo de productos visibles.
     */
    public function index(): View
    {
        // Obtener productos visibles en catálogo con eager loading y paginación
        $productos = Producto::visibleEnCatalogo()
            ->with('variantes') // Eager loading para evitar N+1
            ->paginate(12);

        return view('frontend.catalogo.index', compact('productos'));
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
