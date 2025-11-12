@extends('layouts.encargado')

@section('content')
<div class="max-w-6xl mx-auto py-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Proveedor: {{ $proveedor->nombre_empresa }}</h1>
        <a href="{{ route('encargado.proveedores') }}" class="text-gray-600">Volver al listado</a>
    </div>

    @if(session('status'))
        <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Datos del proveedor -->
        <div class="bg-white p-4 rounded shadow">
            <h2 class="font-semibold mb-2">Datos del Proveedor</h2>
            <div class="text-sm text-gray-700 space-y-1">
                <div><strong>CUIT:</strong> {{ $proveedor->cuit ?? '-' }}</div>
                <div><strong>Email Pedidos:</strong> {{ $proveedor->email_pedidos }}</div>
                <div><strong>Contacto:</strong> {{ $proveedor->nombre_contacto ?? '-' }}</div>
                <div><strong>Teléfono:</strong> {{ $proveedor->telefono ?? '-' }}</div>
                <div><strong>Dirección:</strong> {{ $proveedor->direccion ?? '-' }}</div>
                <div><strong>Notas:</strong> <div class="mt-1 text-sm text-gray-600">{{ $proveedor->notas ?? '-' }}</div></div>
            </div>
            <div class="mt-4">
                <a href="{{ route('encargado.proveedores.edit', $proveedor) }}" class="px-3 py-1 bg-yellow-100 rounded">Editar Datos del Proveedor</a>
            </div>
        </div>

        <!-- Añadir Insumo al Catálogo -->
        <div class="bg-white p-4 rounded shadow lg:col-span-2">
            <h2 class="font-semibold mb-3">Añadir Insumo al Catálogo</h2>

            <form action="{{ route('encargado.proveedor.catalogo.store', $proveedor) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                @csrf

                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700">Insumo</label>
                    <select name="insumo_id" class="mt-1 block w-full border rounded px-2 py-1">
                        <option value="">-- Seleccione --</option>
                        @foreach($insumos_disponibles as $insumo)
                            <option value="{{ $insumo->id }}">{{ $insumo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Precio</label>
                    <input name="precio" type="number" step="0.01" required class="mt-1 block w-full border rounded px-2 py-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Unidad de compra</label>
                    <input name="unidad_de_compra" type="text" placeholder="Ej: Bolsa 25kg" required class="mt-1 block w-full border rounded px-2 py-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Factor de conversión</label>
                    <input name="factor_de_conversion" type="number" step="0.01" required placeholder="Ej: 25000" class="mt-1 block w-full border rounded px-2 py-1" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Tiempo entrega (días)</label>
                    <input name="tiempo_entrega_dias" type="number" step="1" class="mt-1 block w-full border rounded px-2 py-1" />
                </div>

                <div class="md:col-span-3">
                    <button class="mt-2 bg-blue-600 text-white px-4 py-2 rounded">Añadir al Catálogo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Catálogo Actual -->
    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-3">Catálogo Actual</h2>

        @if($proveedor->insumos->isEmpty())
            <div class="text-gray-600">No hay insumos asociados a este proveedor.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-600">Insumo</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-600">Precio</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-600">Unidad</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-600">Factor</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-600">T. Entrega (días)</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($proveedor->insumos as $insumo)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $insumo->nombre }}</td>
                                <td class="px-4 py-2 text-sm">{{ $insumo->pivot->precio }}</td>
                                <td class="px-4 py-2 text-sm">{{ $insumo->pivot->unidad_de_compra }}</td>
                                <td class="px-4 py-2 text-sm">{{ $insumo->pivot->factor_de_conversion }}</td>
                                <td class="px-4 py-2 text-sm">{{ $insumo->pivot->tiempo_entrega_dias ?? '-' }}</td>
                                <td class="px-4 py-2 text-sm space-y-1">
                                    <form action="{{ route('encargado.proveedor.catalogo.update', [$proveedor, $insumo]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-2 items-center">
                                        @csrf
                                        @method('PATCH')
                                        <input name="precio" type="number" step="0.01" value="{{ $insumo->pivot->precio }}" class="px-2 py-1 border rounded" />
                                        <input name="unidad_de_compra" value="{{ $insumo->pivot->unidad_de_compra }}" class="px-2 py-1 border rounded" />
                                        <input name="factor_de_conversion" type="number" step="0.01" value="{{ $insumo->pivot->factor_de_conversion }}" class="px-2 py-1 border rounded" />
                                        <input name="tiempo_entrega_dias" type="number" value="{{ $insumo->pivot->tiempo_entrega_dias }}" class="px-2 py-1 border rounded" />
                                        <div class="flex gap-2">
                                            <button class="px-2 py-1 bg-green-100 text-green-700 rounded">Actualizar</button>
                                        </div>
                                    </form>

                                    <form action="{{ route('encargado.proveedor.catalogo.destroy', [$proveedor, $insumo]) }}" method="POST" onsubmit="return confirm('Quitar insumo del catálogo?');" class="mt-1">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-2 py-1 bg-red-100 text-red-700 rounded">Quitar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
