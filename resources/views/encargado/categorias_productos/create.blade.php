@extends('layouts.encargado')

@section('content')
<div class="max-w-2xl mx-auto py-6">
    <h1 class="text-xl font-semibold mb-4">Crear Nueva Categoría</h1>

    @if($errors->any())
        <div class="mb-4 text-red-700">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('encargado.categorias-productos.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre</label>
                <input name="nombre" value="{{ old('nombre') }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="descripcion" class="mt-1 block w-full border rounded px-3 py-2">{{ old('descripcion') }}</textarea>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
            <a href="{{ route('encargado.categorias-productos.index') }}" class="text-gray-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
