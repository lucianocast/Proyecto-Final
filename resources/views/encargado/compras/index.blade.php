@extends('layouts.encargado')

@section('title', 'Órdenes de Compra')
@section('heading', 'Órdenes de Compra')

@section('content')
<div class="bg-white p-4 rounded shadow">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Listado de Órdenes</h2>
        <a href="{{ route('encargado.compras.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Crear Orden de Compra</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">ID</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Proveedor</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Fecha Emisión</th>
                    <th class="px-4 py-2 text-right text-sm font-medium text-gray-600">Total</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Estado</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($ordenesDeCompra as $oc)
                <tr>
                    <td class="px-4 py-2">{{ $oc->id }}</td>
                    <td class="px-4 py-2">{{ $oc->proveedor->nombre_empresa ?? '-' }}</td>
                    <td class="px-4 py-2">{{ optional($oc->fecha_emision)->format('Y-m-d') ?? $oc->created_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-2 text-right">{{ number_format($oc->total_calculado,2) }}</td>
                    <td class="px-4 py-2">
                        @if(strtolower($oc->status) === 'pendiente')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Pendiente</span>
                        @elseif(strtolower($oc->status) === 'confirmada')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">Confirmada</span>
                        @elseif(strtolower($oc->status) === 'recibida')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Recibida</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded">{{ $oc->status }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right"><a href="{{ route('encargado.compras.show', $oc) }}" class="text-indigo-600">Ver Detalles</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $ordenesDeCompra->links() }}
    </div>
</div>
@endsection
