<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use App\Models\Producto;
use App\Models\Insumo;
use Illuminate\Http\Request;

class RecetaController extends Controller
{
    public function show(Producto $producto)
    {
        $producto->load('receta.insumos');

        $receta = $producto->receta;

        $insumos_disponibles = Insumo::activos()->whereNotIn('id', $receta->insumos->pluck('id'))->orderBy('nombre')->get();

        return view('encargado.recetas.show', compact('producto', 'receta', 'insumos_disponibles'));
    }

    public function store(Request $request, Receta $receta)
    {
        $data = $request->validate([
            'insumo_id' => 'required|exists:insumos,id',
            'cantidad' => 'required|numeric|min:0',
        ]);

        // evitar duplicados
        $exists = $receta->insumos()->where('insumo_id', $data['insumo_id'])->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'El insumo ya está en la receta');
        }

        $receta->insumos()->attach($data['insumo_id'], ['cantidad' => $data['cantidad']]);

        return redirect()->back()->with('status', 'Insumo añadido a la receta');
    }

    public function update(Request $request, Receta $receta, Insumo $insumo)
    {
        $data = $request->validate([
            'cantidad' => 'required|numeric|min:0',
        ]);

        $receta->insumos()->updateExistingPivot($insumo->id, ['cantidad' => $data['cantidad']]);

        return redirect()->back()->with('status', 'Cantidad actualizada');
    }

    public function destroy(Receta $receta, Insumo $insumo)
    {
        $receta->insumos()->detach($insumo->id);
        return redirect()->back()->with('status', 'Insumo removido de la receta');
    }
}
