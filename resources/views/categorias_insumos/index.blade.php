@extends('layouts.encargado')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Categorías de Insumos</h2>
        <a href="{{ route('encargado.categorias-insumos.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded">Crear Nueva Categoría</a>
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
                    <th class="px-6 py-3 text-left">Descripción</th>
                    <th class="px-6 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($categorias as $categoria)
                <tr>
                    <td class="px-6 py-3">{{ $categoria->id }}</td>
                    <td class="px-6 py-3">{{ $categoria->nombre }}</td>
                    <td class="px-6 py-3">{{ $categoria->descripcion }}</td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('encargado.categorias-insumos.edit', $categoria) }}" class="text-blue-600 mr-2">Editar</a>
                        <form action="{{ route('encargado.categorias-insumos.destroy', $categoria) }}" method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('¿Eliminar categoría?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categorias->links() }}
    </div>
</div>
@endsection
