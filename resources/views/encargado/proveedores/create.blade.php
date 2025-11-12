@extends('layouts.encargado')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h1 class="text-xl font-semibold mb-4">Crear Nuevo Proveedor</h1>

    @if($errors->any())
        <div class="mb-4 text-red-700">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('encargado.proveedores.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf

        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Usuario asociado (opcional)</label>
                <select name="user_id" class="mt-1 block w-full border rounded px-3 py-2">
                    <option value="">-- Ninguno --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} &lt;{{ $u->email }}&gt;</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Asocia un usuario del sistema para que pueda acceder al panel de proveedor.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre Empresa <span class="text-red-500">*</span></label>
                <input name="nombre_empresa" value="{{ old('nombre_empresa') }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">CUIT</label>
                <input name="cuit" value="{{ old('cuit') }}" class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre Contacto</label>
                <input name="nombre_contacto" value="{{ old('nombre_contacto') }}" class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email Pedidos <span class="text-red-500">*</span></label>
                <input name="email_pedidos" type="email" value="{{ old('email_pedidos') }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                <input name="telefono" value="{{ old('telefono') }}" class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Dirección</label>
                <input name="direccion" value="{{ old('direccion') }}" class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Notas</label>
                <textarea name="notas" class="mt-1 block w-full border rounded px-3 py-2">{{ old('notas') }}</textarea>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
            <a href="{{ route('encargado.proveedores') }}" class="text-gray-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
