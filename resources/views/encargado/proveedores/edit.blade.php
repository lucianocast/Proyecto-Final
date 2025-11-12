@extends('layouts.encargado')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h1 class="text-xl font-semibold mb-4">Editar Proveedor: {{ $proveedor->nombre_empresa }}</h1>

    @if($errors->any())
        <div class="mb-4 text-red-700">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('encargado.proveedores.update', $proveedor) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre Empresa <span class="text-red-500">*</span></label>
                <input name="nombre_empresa" value="{{ old('nombre_empresa', $proveedor->nombre_empresa) }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">CUIT</label>
                <input name="cuit" value="{{ old('cuit', $proveedor->cuit) }}" class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre Contacto</label>
                <input name="nombre_contacto" value="{{ old('nombre_contacto', $proveedor->nombre_contacto) }}" class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email Pedidos <span class="text-red-500">*</span></label>
                <input name="email_pedidos" type="email" value="{{ old('email_pedidos', $proveedor->email_pedidos) }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input name="telefono" value="{{ old('telefono', $proveedor->telefono) }}" class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Dirección</label>
                <input name="direccion" value="{{ old('direccion', $proveedor->direccion) }}" class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notas</label>
                <textarea name="notas" class="mt-1 block w-full border rounded px-3 py-2">{{ old('notas', $proveedor->notas) }}</textarea>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <button class="bg-green-600 text-white px-4 py-2 rounded">Actualizar</button>
            <a href="{{ route('encargado.proveedores') }}" class="text-gray-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
