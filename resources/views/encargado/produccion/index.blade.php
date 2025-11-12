@extends('layouts.encargado')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Gestionar Productos y Recetas</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('encargado.categorias-productos.index') }}" class="px-3 py-2 bg-gray-100 rounded">Gestionar Categorías</a>
            <a href="{{ route('encargado.productos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">Crear Nuevo Producto</a>
        </div>
    </div>

    @if(session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif

    <div class="bg-white shadow overflow-hidden rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio de Venta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($productos as $producto)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $producto->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $producto->nombre }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $producto->categoria->nombre ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">$ {{ number_format($producto->precio_venta, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('encargado.receta.show', $producto) }}" class="px-2 py-1 bg-gray-100 rounded">Gestionar Receta</a>
                                <a href="{{ route('encargado.productos.edit', $producto) }}" class="px-2 py-1 bg-yellow-100 rounded">Editar</a>
                                <form action="{{ route('encargado.productos.destroy', $producto) }}" method="POST" onsubmit="return confirm('¿Eliminar producto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-2 py-1 bg-red-100 text-red-700 rounded">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay productos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $productos->links() }}
    </div>
</div>
@endsection
@extends('layouts.encargado')

@section('title', 'Producción')
@section('heading', 'Gestionar producción')

@section('content')
    <div class="bg-white p-4 rounded shadow">
        <p>Aquí irá el flujo de producción y órdenes.</p>
    </div>
@endsection
