@extends('layouts.encargado')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold">Gestionar Receta para: {{ $producto->nombre }}</h1>
        <a href="{{ route('encargado.produccion') }}" class="text-gray-600">Volver a Productos</a>
    </div>

    @if(session('status'))
        <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-3">Añadir Insumo a la Receta</h2>
        <form action="{{ route('encargado.receta.insumo.store', $receta) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Insumo</label>
                <select id="insumo-select" name="insumo_id" class="mt-1 block w-full border rounded px-2 py-1">
                    <option value="">-- Seleccione --</option>
                    @foreach($insumos_disponibles as $insumo)
                        <option value="{{ $insumo->id }}" data-unit="{{ $insumo->unidad_de_medida }}">{{ $insumo->nombre }} ({{ $insumo->unidad_de_medida }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                <input name="cantidad" type="number" step="0.01" required class="mt-1 block w-full border rounded px-2 py-1" />
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-medium text-gray-700">Unidad (info)</label>
                <div class="mt-1 text-sm text-gray-600">Unidad: <span id="insumo-unidad">-</span></div>
            </div>

            <div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Añadir Insumo</button>
            </div>
        </form>
    </div>

<script>
    (function(){
        const select = document.getElementById('insumo-select');
        const unidadSpan = document.getElementById('insumo-unidad');

        function updateUnidad() {
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.dataset) {
                unidadSpan.innerText = '-';
                return;
            }
            const unit = opt.dataset.unit || '-';
            unidadSpan.innerText = unit;
        }

        if (select) {
            select.addEventListener('change', updateUnidad);
            // inicializar
            updateUnidad();
        }
    })();
</script>

    <div class="bg-white p-4 rounded shadow">
        <h2 class="font-semibold mb-3">Insumos en la Receta</h2>

        @if($receta->insumos->isEmpty())
            <div class="text-gray-600">No hay insumos en esta receta.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs text-gray-600">Insumo</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-600">Cantidad</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-600">Unidad</th>
                            <th class="px-4 py-2 text-left text-xs text-gray-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($receta->insumos as $insumo)
                            <tr>
                                <td class="px-4 py-2 text-sm">{{ $insumo->nombre }}</td>
                                <td class="px-4 py-2 text-sm">{{ $insumo->pivot->cantidad }}</td>
                                <td class="px-4 py-2 text-sm">{{ $insumo->unidad_de_medida }}</td>
                                <td class="px-4 py-2 text-sm space-y-1">
                                    <form action="{{ route('encargado.receta.insumo.update', [$receta, $insumo]) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                                        @csrf
                                        @method('PATCH')
                                        <input name="cantidad" type="number" step="0.01" value="{{ $insumo->pivot->cantidad }}" class="px-2 py-1 border rounded" />
                                        <div class="md:col-span-2 flex gap-2">
                                            <button class="px-2 py-1 bg-green-100 text-green-700 rounded">Actualizar</button>
                                        </div>
                                    </form>

                                    <form action="{{ route('encargado.receta.insumo.destroy', [$receta, $insumo]) }}" method="POST" onsubmit="return confirm('Quitar insumo de la receta?');">
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
