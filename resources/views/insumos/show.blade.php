@extends('layouts.encargado')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-semibold">Gestionar Stock de: {{ $insumo->nombre }}</h2>
            <p class="text-sm text-gray-600">Stock Total Actual: <span class="font-bold">{{ $insumo->stock_total }} {{ $insumo->unidad_de_medida }}</span></p>
        </div>
        <a href="{{ route('encargado.insumos.index') }}" class="text-gray-600">Volver</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-2 bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Añadir nuevo lote -->
        <div class="bg-white p-4 rounded shadow">
            <h3 class="font-semibold mb-3">Añadir Nuevo Lote</h3>
            <form action="{{ route('encargado.stock.store', $insumo) }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="block">Cantidad</label>
                    <input name="cantidad" type="number" step="0.01" value="{{ old('cantidad') }}" class="w-full border rounded px-2 py-1" required />
                </div>
                <div class="mb-2">
                    <label class="block">Fecha de vencimiento</label>
                    <input name="fecha_vencimiento" type="date" value="{{ old('fecha_vencimiento') }}" class="w-full border rounded px-2 py-1" />
                </div>
                <div class="mb-2">
                    <label class="block">Código de Lote (opcional)</label>
                    <input name="codigo_lote" value="{{ old('codigo_lote') }}" class="w-full border rounded px-2 py-1" />
                </div>
                <div class="mt-4">
                    <button class="bg-blue-600 text-white px-3 py-1 rounded">Añadir Lote</button>
                </div>
            </form>
        </div>

        <!-- Lotes actuales -->
        <div class="bg-white p-4 rounded shadow col-span-1 md:col-span-2">
            <h3 class="font-semibold mb-3">Lotes Actuales</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">ID Lote</th>
                            <th class="px-4 py-2 text-left">Cód. Lote</th>
                            <th class="px-4 py-2 text-left">Cantidad Inicial</th>
                            <th class="px-4 py-2 text-left">Cantidad Actual</th>
                            <th class="px-4 py-2 text-left">Fecha Vencimiento</th>
                            <th class="px-4 py-2 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($insumo->lotes as $lote)
                        <tr>
                            <td class="px-4 py-2">{{ $lote->id }}</td>
                            <td class="px-4 py-2">{{ $lote->codigo_lote ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $lote->cantidad_inicial }}</td>
                            <td class="px-4 py-2">{{ $lote->cantidad_actual }}</td>
                            <td class="px-4 py-2">{{ $lote->fecha_vencimiento?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-4 py-2 text-right">
                                <details class="inline-block">
                                    <summary class="cursor-pointer text-blue-600">Ajustar</summary>
                                    <form action="{{ route('encargado.stock.update', $lote) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex items-center gap-2">
                                            <input name="cantidad_actual" type="number" step="0.01" value="{{ old('cantidad_actual', $lote->cantidad_actual) }}" class="w-40 border rounded px-2 py-1" />
                                            <button class="bg-yellow-500 text-white px-3 py-1 rounded">Guardar</button>
                                        </div>
                                    </form>
                                </details>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
