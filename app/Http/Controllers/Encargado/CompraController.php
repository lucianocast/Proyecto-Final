<?php

namespace App\Http\Controllers\Encargado;

use App\Http\Controllers\Controller;
use App\Models\OrdenDeCompra;
use App\Models\OrdenDeCompraItem;
use App\Models\Proveedor;
use App\Models\Insumo;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        $ordenesDeCompra = OrdenDeCompra::with('proveedor','items.insumo')->orderBy('created_at','desc')->paginate(20);
        return view('encargado.compras.index', compact('ordenesDeCompra'));
    }

    public function create()
    {
        $proveedores = Proveedor::activos()->orderBy('nombre_empresa')->get();
        $insumos = Insumo::activos()->orderBy('nombre')->get();
        return view('encargado.compras.create', compact('proveedores','insumos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha_emision' => 'required|date',
            'fecha_entrega_esperada' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.insumo_id' => 'required|exists:insumos,id',
            'items.*.cantidad' => 'required|numeric|min:0.0001',
        ]);

        DB::beginTransaction();
        try {
            $oc = OrdenDeCompra::create([
                'proveedor_id' => $data['proveedor_id'],
                'user_id' => Auth::id(),
                'status' => 'Pendiente',
                'fecha_emision' => $data['fecha_emision'],
                'fecha_entrega_esperada' => $data['fecha_entrega_esperada'] ?? null,
                'total_calculado' => 0,
            ]);

            $total = 0;
            foreach ($data['items'] as $it) {
                // tomar precio actual desde insumo_proveedor si existe
                $precio = Insumo::find($it['insumo_id'])->proveedores()->where('proveedor_id', $data['proveedor_id'])->pluck('insumo_proveedor.precio')->first() ?? 0;
                $subtotal = $precio * $it['cantidad'];
                OrdenDeCompraItem::create([
                    'orden_de_compra_id' => $oc->id,
                    'insumo_id' => $it['insumo_id'],
                    'cantidad' => $it['cantidad'],
                    'precio_unitario' => $precio,
                    'subtotal' => $subtotal,
                ]);
                $total += $subtotal;
            }

            $oc->total_calculado = $total;
            $oc->save();

            DB::commit();
            return redirect()->route('encargado.compras')->with('status', 'Orden de compra creada');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creando la OC: '.$e->getMessage());
        }
    }

    public function show(OrdenDeCompra $ordenDeCompra)
    {
        $ordenDeCompra->load('proveedor','items.insumo');
        return view('encargado.compras.show', compact('ordenDeCompra'));
    }

    public function receive(Request $request, OrdenDeCompra $ordenDeCompra)
    {
        // Validar que la OC esté en estado adecuado
        if ($ordenDeCompra->status !== 'Confirmada' && $ordenDeCompra->status !== 'Pendiente') {
            return redirect()->back()->with('error', 'La orden no está en estado para recibir');
        }

        $data = $request->validate([
            // fecha y codigo por item son opcionales, serán nombres dinamicos
        ]);

        DB::beginTransaction();
        try {
            foreach ($ordenDeCompra->items as $item) {
                $fecha_venc = $request->input('fecha_vencimiento_item_' . $item->id) ?? null;
                $codigo = $request->input('codigo_lote_item_' . $item->id) ?? null;

                Lote::create([
                    'insumo_id' => $item->insumo_id,
                    'cantidad_inicial' => $item->cantidad,
                    'cantidad_actual' => $item->cantidad,
                    'fecha_vencimiento' => $fecha_venc,
                    'codigo_lote' => $codigo,
                ]);
            }

            $ordenDeCompra->status = 'Recibida';
            $ordenDeCompra->save();

            DB::commit();
            return redirect()->route('encargado.compras')->with('status','OC recibida y stock actualizado');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al recibir OC: '.$e->getMessage());
        }
    }
}
