<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\OrdenDeCompra;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanelController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $proveedor = $user->proveedor;
        if (! $proveedor) {
            // Redirect to home if no proveedor profile is linked to the user
            return redirect('/')->with('error','No se encontró proveedor para el usuario');
        }

        $ocsPendientes = OrdenDeCompra::where('proveedor_id', $proveedor->id)
            ->where('status', 'Pendiente')
            ->with('items.insumo')
            ->orderBy('created_at','desc')
            ->get();

        $ocsConfirmadas = OrdenDeCompra::where('proveedor_id', $proveedor->id)
            ->where('status', 'Confirmada')
            ->with('items.insumo')
            ->orderBy('created_at','desc')
            ->get();

        return view('proveedor.dashboard', compact('ocsPendientes','ocsConfirmadas','proveedor'));
    }

    public function confirm(OrdenDeCompra $ordenDeCompra)
    {
        $user = Auth::user();
        $proveedor = $user->proveedor;
        if (! $proveedor || $ordenDeCompra->proveedor_id !== $proveedor->id) {
            return redirect()->back()->with('error','No autorizado');
        }
        if ($ordenDeCompra->status !== 'Pendiente') {
            return redirect()->back()->with('error','Solo se pueden confirmar OC Pendientes');
        }
        $ordenDeCompra->status = 'Confirmada';
        $ordenDeCompra->save();
        return redirect()->back()->with('status','Orden confirmada');
    }

    public function reject(OrdenDeCompra $ordenDeCompra, Request $request)
    {
        $user = Auth::user();
        $proveedor = $user->proveedor;
        if (! $proveedor || $ordenDeCompra->proveedor_id !== $proveedor->id) {
            return redirect()->back()->with('error','No autorizado');
        }
        $ordenDeCompra->status = 'Rechazada';
        $ordenDeCompra->save();
        return redirect()->back()->with('status','Orden rechazada');
    }
}
