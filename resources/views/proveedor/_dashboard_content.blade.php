<div class="space-y-6">
    <h1 class="text-2xl font-semibold">Bienvenido, {{ $proveedor->nombre_empresa }}</h1>

    <section>
        <h2 class="text-xl font-medium mb-3">Órdenes de Compra Pendientes</h2>
        <div class="grid grid-cols-1 gap-4">
            @forelse($ocsPendientes as $oc)
                <div class="p-4 bg-white rounded shadow">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500">OC #{{ $oc->id }} - {{ optional($oc->fecha_emision)->format('Y-m-d') ?? $oc->created_at->format('Y-m-d') }}</div>
                            <div class="mt-2">
                                <ul class="list-disc pl-5 text-sm">
                                    @foreach($oc->items as $item)
                                        <li>{{ $item->insumo->nombre }} — {{ $item->cantidad }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="space-x-2">
                            <form method="POST" action="{{ route('proveedor.oc.confirm', $oc) }}" style="display:inline">@csrf<button class="px-3 py-1 bg-green-600 text-white rounded">Confirmar Pedido</button></form>
                            <form method="POST" action="{{ route('proveedor.oc.reject', $oc) }}" style="display:inline">@csrf<button class="px-3 py-1 bg-red-600 text-white rounded">Rechazar Pedido</button></form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-4 bg-white rounded shadow">No hay órdenes pendientes.</div>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="text-xl font-medium mb-3">Órdenes Confirmadas (En Preparación)</h2>
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">Fecha Emisión</th>
                        <th class="px-3 py-2 text-left">Fecha Entrega Esperada</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ocsConfirmadas as $oc)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $oc->id }}</td>
                            <td class="px-3 py-2">{{ optional($oc->fecha_emision)->format('Y-m-d') ?? $oc->created_at->format('Y-m-d') }}</td>
                            <td class="px-3 py-2">{{ optional($oc->fecha_entrega_esperada)->format('Y-m-d') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td class="p-4">No hay órdenes confirmadas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
