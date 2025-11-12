@extends('layouts.encargado')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-semibold">Gestionar Stock</h2>
            <p class="text-sm text-gray-600">Lista de insumos y su stock total por lotes.</p>
        </div>
        <a href="{{ route('encargado.insumos.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded">Crear Nuevo Insumo</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-2 bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">ID</th>
                    <th class="px-6 py-3 text-left">Nombre</th>
                    <th class="px-6 py-3 text-left">Categoría</th>
                    <th class="px-6 py-3 text-left">Stock Total</th>
                    <th class="px-6 py-3 text-left">Unidad</th>
                    <th class="px-6 py-3 text-left">Stock Mínimo</th>
                    <th class="px-6 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($insumos as $insumo)
                <tr>
                    <td class="px-6 py-3">{{ $insumo->id }}</td>
                    <td class="px-6 py-3">{{ $insumo->nombre }}</td>
                    <td class="px-6 py-3">{{ $insumo->categoria->nombre ?? '-' }}</td>
                    <td class="px-6 py-3">
                        <span @if($insumo->stock_total <= $insumo->stock_minimo) class="text-red-500 font-bold" @endif>
                            {{ $insumo->stock_total }}
                        </span>
                    </td>
                    <td class="px-6 py-3">{{ $insumo->unidad_de_medida }}</td>
                    <td class="px-6 py-3">{{ $insumo->stock_minimo }}</td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('encargado.insumos.show', $insumo) }}" class="text-green-600 mr-2">Gestionar Lotes</a>
                        <a href="{{ route('encargado.insumos.edit', $insumo) }}" class="text-blue-600 mr-2">Editar</a>
                        <form action="{{ route('encargado.insumos.destroy', $insumo) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('¿Eliminar insumo?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $insumos->links() }}
    </div>
</div>
@endsection
@extends('layouts.encargado')

@section('title', 'Stock')
@section('heading', 'Gestionar stock')

@section('content')
    <div class="bg-white p-4 rounded shadow">
        <p>Aquí irá el inventario y control de existencias.</p>
    </div>
@endsection
