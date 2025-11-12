@extends('layouts.encargado')

@section('title', 'Detalle Orden de Compra')
@section('heading', 'Detalle de Orden de Compra')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <h2 class="text-lg font-semibold mb-2">Detalle de Orden de Compra #{{ $ordenDeCompra->id }}</h2>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <p><strong>Proveedor:</strong> {{ $ordenDeCompra->proveedor->nombre_empresa ?? '-' }}</p>
            <p><strong>Fecha Emisión:</strong> {{ optional($ordenDeCompra->fecha_emision)->format('Y-m-d') ?? $ordenDeCompra->created_at->format('Y-m-d') }}</p>
        </div>
        <div class="text-right">
            <p><strong>Estado:</strong> {{ $ordenDeCompra->status }}</p>
            <p><strong>Total:</strong> {{ number_format($ordenDeCompra->total_calculado,2) }}</p>
        </div>
    </div>

    <h3 class="font-semibold">Items de la Orden</h3>
    <div class="overflow-x-auto mb-4">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Insumo</th>
                    <th class="px-3 py-2 text-right">Cantidad</th>
                    <th class="px-3 py-2 text-right">Precio unitario</th>
                    <th class="px-3 py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($ordenDeCompra->items as $item)
                <tr>
                    <td class="px-3 py-2">{{ $item->insumo->nombre }}</td>
                    <td class="px-3 py-2 text-right">{{ $item->cantidad }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($item->precio_unitario,2) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($item->subtotal,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Sección de recepción --}}
    @if(strtolower($ordenDeCompra->status) === 'confirmada')
        <div class="mt-4 bg-gray-50 p-4 rounded">
            <h4 class="font-semibold mb-2">Recibir Mercadería</h4>
            <form method="POST" action="{{ route('encargado.compras.receive', $ordenDeCompra) }}">
                @csrf
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white">
                            <tr>
                                <th class="px-3 py-2 text-left">Insumo</th>
                                <th class="px-3 py-2 text-right">Cantidad Pedida</th>
                                <th class="px-3 py-2 text-left">Fecha Vencimiento</th>
                                <th class="px-3 py-2 text-left">Código Lote</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ordenDeCompra->items as $item)
                                <tr>
                                    <td class="px-3 py-2">{{ $item->insumo->nombre }}</td>
                                    <td class="px-3 py-2 text-right">{{ $item->cantidad }}</td>
                                    <td class="px-3 py-2"><input type="date" name="fecha_vencimiento_item_{{ $item->id }}" class="border-gray-300 rounded" /></td>
                                    <td class="px-3 py-2"><input type="text" name="codigo_lote_item_{{ $item->id }}" class="border-gray-300 rounded w-full" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-right">
                    <button class="px-4 py-2 bg-green-600 text-white rounded">Confirmar Recepción de Mercadería</button>
                </div>
            </form>
        </div>

    @elseif(strtolower($ordenDeCompra->status) === 'pendiente')
        <div class="p-4 bg-yellow-50 rounded">Esperando confirmación del proveedor.</div>

    @elseif(strtolower($ordenDeCompra->status) === 'recibida')
        <div class="p-4 bg-green-50 rounded">Esta orden ya fue recibida y el stock fue actualizado.</div>
    @endif

</div>
@endsection
