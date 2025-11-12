@extends('layouts.encargado')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-4">Editar Categoría: {{ $categoria->nombre }}</h2>

    @if($errors->any())
        <div class="mb-4 text-red-700">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('encargado.categorias-insumos.update', $categoria) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-2">
            <label class="block">Nombre</label>
            <input name="nombre" value="{{ old('nombre', $categoria->nombre) }}" class="w-full border rounded px-2 py-1" />
        </div>
        <div class="mb-2">
            <label class="block">Descripción</label>
            <textarea name="descripcion" class="w-full border rounded px-2 py-1">{{ old('descripcion', $categoria->descripcion) }}</textarea>
        </div>

        <div class="mt-4">
            <button class="bg-blue-600 text-white px-3 py-1 rounded">Guardar</button>
            <a href="{{ route('encargado.categorias-insumos.index') }}" class="ml-2 text-gray-600">Cancelar</a>
        </div>
    </form>
</div>
@endsection
