<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Insumo;
use Illuminate\Http\Request;

class ProveedorInsumoController extends Controller
{
    public function store(Request $request, Proveedor $proveedor)
    {
        $data = $request->validate([
            'insumo_id' => 'required|exists:insumos,id',
            'precio' => 'required|numeric|min:0',
            'unidad_de_compra' => 'required|string|max:255',
            'factor_de_conversion' => 'required|numeric|min:0',
            'tiempo_entrega_dias' => 'nullable|integer|min:0',
        ]);

        // evitar duplicados - si ya existe, actualizar pivot
        $exists = $proveedor->insumos()->where('insumo_id', $data['insumo_id'])->exists();
        if ($exists) {
            $proveedor->insumos()->updateExistingPivot($data['insumo_id'], [
                'precio' => $data['precio'],
                'unidad_de_compra' => $data['unidad_de_compra'],
                'factor_de_conversion' => $data['factor_de_conversion'],
                'tiempo_entrega_dias' => $data['tiempo_entrega_dias'] ?? null,
            ]);
        } else {
            $proveedor->insumos()->attach($data['insumo_id'], [
                'precio' => $data['precio'],
                'unidad_de_compra' => $data['unidad_de_compra'],
                'factor_de_conversion' => $data['factor_de_conversion'],
                'tiempo_entrega_dias' => $data['tiempo_entrega_dias'] ?? null,
            ]);
        }

        return redirect()->back()->with('success', 'Catálogo actualizado');
    }

    public function update(Request $request, Proveedor $proveedor, Insumo $insumo)
    {
        $data = $request->validate([
            'precio' => 'required|numeric|min:0',
            'unidad_de_compra' => 'required|string|max:255',
            'factor_de_conversion' => 'required|numeric|min:0',
            'tiempo_entrega_dias' => 'nullable|integer|min:0',
        ]);

        $proveedor->insumos()->updateExistingPivot($insumo->id, [
            'precio' => $data['precio'],
            'unidad_de_compra' => $data['unidad_de_compra'],
            'factor_de_conversion' => $data['factor_de_conversion'],
            'tiempo_entrega_dias' => $data['tiempo_entrega_dias'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Entrada del catálogo actualizada');
    }

    public function destroy(Proveedor $proveedor, Insumo $insumo)
    {
        $proveedor->insumos()->detach($insumo->id);

        return redirect()->back()->with('success', 'Insumo removido del catálogo');
    }
}
