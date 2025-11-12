@extends('layouts.encargado')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h2 class="text-lg font-semibold mb-4">Editar Insumo: {{ $insumo->nombre }}</h2>

    @if($errors->any())
        <div class="mb-4 text-red-700">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('encargado.insumos.update', $insumo) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-2">
            <label class="block">Nombre</label>
            <input name="nombre" value="{{ old('nombre', $insumo->nombre) }}" class="w-full border rounded px-2 py-1" />
        </div>

        <div class="mb-2">
            <label class="block">Descripción</label>
            <textarea name="descripcion" class="w-full border rounded px-2 py-1">{{ old('descripcion', $insumo->descripcion) }}</textarea>
        </div>

        <div class="mb-2">
            <label class="block">Categoría</label>
            <div class="flex gap-2">
                <select name="categoria_insumo_id" class="flex-1 border rounded px-2 py-1">
                    @foreach($categorias as $c)
                        <option value="{{ $c->id }}" {{ (old('categoria_insumo_id', $insumo->categoria_insumo_id) == $c->id) ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
                <a href="{{ route('encargado.categorias-insumos.create') }}" class="inline-flex items-center px-3 py-1 bg-gray-100 rounded border text-sm text-gray-700">Nueva categoría</a>
            </div>
        </div>

        <div class="mb-2">
            <label class="block">Unidad de medida</label>
            <input name="unidad_de_medida" value="{{ old('unidad_de_medida', $insumo->unidad_de_medida) }}" class="w-full border rounded px-2 py-1" placeholder="ej: g, ml, un" />
        </div>

        <div class="mb-2">
            <label class="block">Stock mínimo</label>
            <input name="stock_minimo" type="number" step="0.01" value="{{ old('stock_minimo', $insumo->stock_minimo) }}" class="w-full border rounded px-2 py-1" />
        </div>

        <div class="mb-2">
            <label class="block">Ubicación</label>
            <input name="ubicacion" value="{{ old('ubicacion', $insumo->ubicacion) }}" class="w-full border rounded px-2 py-1" />
        </div>

        <div class="mt-4">
            <button class="bg-blue-600 text-white px-3 py-1 rounded">Guardar</button>
            <a href="{{ route('encargado.insumos.index') }}" class="ml-2 text-gray-600">Cancelar</a>
        </div>
    </form>

    <!-- Modal: Crear categoría inline (mismo comportamiento que en create) -->
    <div id="categoria-modal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">
        <div class="bg-white rounded shadow-lg w-full max-w-md p-4">
            <h3 class="font-semibold mb-2">Crear nueva categoría</h3>
            <form id="categoria-form">
                @csrf
                <div class="mb-2">
                    <label class="block">Nombre</label>
                    <input id="categoria-nombre" name="nombre" class="w-full border rounded px-2 py-1" />
                </div>
                <div class="mb-2">
                    <label class="block">Descripción</label>
                    <textarea id="categoria-descripcion" name="descripcion" class="w-full border rounded px-2 py-1"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="categoria-cancel" class="px-3 py-1 rounded border">Cancelar</button>
                    <button type="submit" id="categoria-submit" class="px-3 py-1 rounded bg-blue-600 text-white">Crear</button>
                </div>
                <div id="categoria-errors" class="mt-2 text-sm text-red-600"></div>
            </form>
        </div>
    </div>

    <script>
        (function(){
            const modal = document.getElementById('categoria-modal');
            const newBtn = document.querySelector('a[href="{{ route('encargado.categorias-insumos.create') }}"]');
            if (newBtn) {
                newBtn.addEventListener('click', function(e){
                    e.preventDefault();
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                });
            }

            document.getElementById('categoria-cancel').addEventListener('click', function(){
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.getElementById('categoria-errors').innerHTML = '';
            });

            document.getElementById('categoria-form').addEventListener('submit', async function(e){
                e.preventDefault();
                const nombre = document.getElementById('categoria-nombre').value.trim();
                const descripcion = document.getElementById('categoria-descripcion').value.trim();
                const errorsDiv = document.getElementById('categoria-errors');
                errorsDiv.innerHTML = '';

                const token = document.querySelector('input[name="_token"]').value;

                try {
                    const res = await fetch('{{ route('encargado.categorias-insumos.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({ nombre, descripcion })
                    });

                    if (!res.ok) {
                        const data = await res.json().catch(()=>null);
                        if (data && data.errors) {
                            errorsDiv.innerHTML = Object.values(data.errors).flat().join('<br>');
                        } else {
                            errorsDiv.innerText = 'Error al crear categoría';
                        }
                        return;
                    }

                    const data = await res.json();
                    const categoria = data.categoria;
                    const select = document.querySelector('select[name="categoria_insumo_id"]');
                    if (select) {
                        const opt = document.createElement('option');
                        opt.value = categoria.id;
                        opt.text = categoria.nombre;
                        opt.selected = true;
                        select.appendChild(opt);
                    }

                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.getElementById('categoria-nombre').value = '';
                    document.getElementById('categoria-descripcion').value = '';

                } catch (err) {
                    errorsDiv.innerText = 'Error de red';
                }
            });
        })();
    </script>
</div>
@endsection
