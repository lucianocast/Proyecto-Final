@extends('layouts.encargado')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Categoría: {{ $categoria->nombre }}</h2>
        <div>
            <a href="{{ route('encargado.categorias-insumos.edit', $categoria) }}" class="text-blue-600 mr-2">Editar</a>
            <form action="{{ route('encargado.categorias-insumos.destroy', $categoria) }}" method="POST" style="display:inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600" onclick="return confirm('¿Eliminar categoría?')">Eliminar</button>
            </form>
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <p><strong>Nombre:</strong> {{ $categoria->nombre }}</p>
        <p class="mt-2"><strong>Descripción:</strong></p>
        <p class="text-gray-700">{{ $categoria->descripcion ?? '-' }}</p>
    </div>
</div>
@endsection
