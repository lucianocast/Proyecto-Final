@extends('layouts.encargado')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h1 class="text-xl font-semibold mb-4">Editar Producto: {{ $producto->nombre }}</h1>

    @if($errors->any())
        <div class="mb-4 text-red-700">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('encargado.productos.update', $producto) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
                <input name="nombre" value="{{ old('nombre', $producto->nombre) }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="descripcion" class="mt-1 block w-full border rounded px-3 py-2">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Categoría</label>
                <select name="categoria_producto_id" class="mt-1 block w-full border rounded px-3 py-2">
                    @foreach($categorias as $c)
                        <option value="{{ $c->id }}" {{ old('categoria_producto_id', $producto->categoria_producto_id) == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Precio de Venta <span class="text-red-500">*</span></label>
                <input name="precio_venta" type="number" step="0.01" value="{{ old('precio_venta', $producto->precio_venta) }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <button class="bg-green-600 text-white px-4 py-2 rounded">Actualizar</button>
            <a href="{{ route('encargado.produccion') }}" class="text-gray-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
