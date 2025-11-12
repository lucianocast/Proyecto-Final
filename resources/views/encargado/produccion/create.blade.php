@extends('layouts.encargado')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h1 class="text-xl font-semibold mb-4">Crear Nuevo Producto</h1>

    @if($errors->any())
        <div class="mb-4 text-red-700">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('encargado.productos.store') }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nombre <span class="text-red-500">*</span></label>
                <input name="nombre" value="{{ old('nombre') }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                <textarea name="descripcion" class="mt-1 block w-full border rounded px-3 py-2">{{ old('descripcion') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Categoría</label>
                <div class="flex gap-2">
                    <select name="categoria_producto_id" class="flex-1 mt-1 block w-full border rounded px-3 py-2">
                        @foreach($categorias as $c)
                            <option value="{{ $c->id }}" {{ old('categoria_producto_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                    <button id="nueva-categoria-btn" type="button" class="mt-1 px-3 py-1 bg-gray-100 rounded border text-sm">Nueva categoría</button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Precio de Venta <span class="text-red-500">*</span></label>
                <input name="precio_venta" type="number" step="0.01" value="{{ old('precio_venta') }}" required class="mt-1 block w-full border rounded px-3 py-2" />
            </div>
        </div>

        <div class="mt-4 flex items-center gap-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
            <a href="{{ route('encargado.produccion') }}" class="text-gray-600">Cancelar</a>
        </div>
    </form>

    <!-- Modal: Crear categoría inline -->
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
            const newBtn = document.getElementById('nueva-categoria-btn');
            // If user clicks the 'Nueva categoría' button, open modal instead of navigating
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
                    const res = await fetch('{{ route('encargado.categorias-productos.store') }}', {
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
                    // Añadir opción al select
                    const select = document.querySelector('select[name="categoria_producto_id"]');
                    if (select) {
                        const opt = document.createElement('option');
                        opt.value = categoria.id;
                        opt.text = categoria.nombre;
                        opt.selected = true;
                        select.appendChild(opt);
                    }

                    // Cerrar modal
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
