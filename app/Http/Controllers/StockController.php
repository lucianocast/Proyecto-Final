<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\Lote;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Añadir stock creando un nuevo lote
     */
    public function store(Request $request, Insumo $insumo)
    {
        $data = $request->validate([
            'cantidad' => 'required|numeric|min:0.0001',
            'fecha_vencimiento' => 'nullable|date',
            'codigo_lote' => 'nullable|string|max:255',
        ]);

        $lote = Lote::create([
            'insumo_id' => $insumo->id,
            'cantidad_inicial' => $data['cantidad'],
            'cantidad_actual' => $data['cantidad'],
            'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
            'codigo_lote' => $data['codigo_lote'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Lote agregado');
    }

    /**
     * Ajustar cantidad_actual de un lote (mermas, roturas)
     */
    public function update(Request $request, Lote $lote)
    {
        $data = $request->validate([
            'cantidad_actual' => 'required|numeric|min:0',
        ]);

        // No permitir cantidad_actual mayor que cantidad_inicial
        $nueva = $data['cantidad_actual'];
        if ($nueva > $lote->cantidad_inicial) {
            return redirect()->back()->with('error', 'La cantidad actual no puede exceder la cantidad inicial del lote');
        }

        $lote->cantidad_actual = $nueva;
        $lote->save();

        return redirect()->back()->with('success', 'Lote ajustado');
    }
}
