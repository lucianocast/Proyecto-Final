@extends('layouts.encargado')

@section('title', 'Crear Nueva Orden de Compra')
@section('heading', 'Crear Nueva Orden de Compra')

@section('content')
<div class="bg-white p-6 rounded shadow">
    <form action="{{ route('encargado.compras.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Proveedor</label>
                <select name="proveedor_id" class="mt-1 block w-full border-gray-300 rounded">
                    @foreach($proveedores as $p)
                        <option value="{{ $p->id }}">{{ $p->nombre_empresa }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha entrega esperada</label>
                <input type="date" name="fecha_entrega_esperada" class="mt-1 block w-full border-gray-300 rounded" />
            </div>
        </div>

        <h3 class="font-semibold mb-2">Items de la Orden</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Insumo</th>
                        <th class="px-3 py-2 text-left">Cantidad</th>
                        <th class="px-3 py-2 text-left">Precio unitario</th>
                        <th class="px-3 py-2"></th>
                    </tr>
                </thead>
                <tbody id="items-tbody">
                    <tr class="item-row">
                        <td class="px-3 py-2">
                            <select name="items[0][insumo_id]" class="border-gray-300 rounded w-full">
                                @foreach($insumos as $ins)
                                    <option value="{{ $ins->id }}">{{ $ins->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-3 py-2"><input type="number" step="0.01" name="items[0][cantidad]" class="border-gray-300 rounded w-full" /></td>
                        <td class="px-3 py-2"><input type="number" step="0.01" name="items[0][precio_unitario]" class="border-gray-300 rounded w-full" /></td>
                        <td class="px-3 py-2"><button type="button" class="remove-row px-2 py-1 bg-red-500 text-white rounded">Eliminar</button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <button type="button" id="add-item" class="px-3 py-2 bg-gray-200 rounded">Añadir Insumo</button>
        </div>

        <div class="mt-6 text-right">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Guardar Orden de Compra</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    let idx = 1;
    const addBtn = document.getElementById('add-item');
    const tbody = document.getElementById('items-tbody');

    addBtn.addEventListener('click', function(){
        const template = document.querySelector('.item-row');
        const clone = template.cloneNode(true);
        // update names
        clone.querySelectorAll('select, input').forEach(function(el){
            if(el.name){
                el.name = el.name.replace(/items\[0\]/, 'items['+idx+']');
                if(el.tagName === 'INPUT') el.value = '';
            }
        });
        tbody.appendChild(clone);
        idx++;
    });

    tbody.addEventListener('click', function(e){
        if(e.target && e.target.classList.contains('remove-row')){
            const rows = tbody.querySelectorAll('.item-row');
            if(rows.length > 1){
                e.target.closest('.item-row').remove();
            }
        }
    });
});
</script>

@endsection
