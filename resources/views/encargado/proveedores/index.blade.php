@extends('layouts.encargado')

@section('content')
<div class="max-w-6xl mx-auto py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Gestionar Proveedores</h1>
        <a href="{{ route('encargado.proveedores.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded shadow-sm">Crear Nuevo Proveedor</a>
    </div>

    @if(session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow overflow-hidden rounded">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Empresa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Pedidos</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($proveedores as $proveedor)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $proveedor->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">{{ $proveedor->nombre_empresa }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $proveedor->email_pedidos }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $proveedor->telefono ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('encargado.proveedores.show', $proveedor) }}" class="text-sm px-2 py-1 bg-gray-100 rounded">Gestionar Catálogo</a>
                                <a href="{{ route('encargado.proveedores.edit', $proveedor) }}" class="text-sm px-2 py-1 bg-yellow-100 rounded">Editar</a>
                                <form action="{{ route('encargado.proveedores.destroy', $proveedor) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas dar de baja este proveedor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm px-2 py-1 bg-red-100 text-red-700 rounded">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay proveedores aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $proveedores->links() }}
    </div>
</div>
@endsection
@extends('layouts.encargado')

@section('title', 'Proveedores')
@section('heading', 'Gestionar proveedores')

@section('content')
    <div class="bg-white p-4 rounded shadow">
        <p>Aquí irá la lista/formulario de proveedores.</p>
    </div>
@endsection
