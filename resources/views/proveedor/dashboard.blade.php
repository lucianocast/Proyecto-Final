@extends('layouts.encargado')

@section('title', 'Panel Proveedor')

@php
    // Lógica para asegurar que las variables existan
    $ocsPendientes = $ocsPendientes ?? collect();
    $ocsConfirmadas = $ocsConfirmadas ?? collect();
@endphp

@section('content')

<div class="space-y-8">
    
    <h1 class="text-3xl font-bold text-gray-800">Bienvenido, {{ $proveedor->nombre_empresa }}</h1>

    <section>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-300">
            Órdenes de Compra Pendientes
            <span class="ml-2 inline-flex items-center justify-center px-3 py-1 text-sm font-medium leading-5 text-white bg-red-600 rounded-full">
                {{ $ocsPendientes->count() }}
            </span>
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($ocsPendientes as $oc)
                <div class="bg-white/40 backdrop-blur-lg border border-white/20 rounded-2xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl hover:scale-[1.02]">
                    <div class="flex flex-col h-full">
                        <div class="flex justify-between items-center mb-4">
                            <span class="font-bold text-lg text-indigo-800">Orden de Compra #{{ $oc->id }}</span>
                            <span class="text-sm text-gray-600">{{ optional($oc->fecha_emision)->format('d/m/Y') ?? $oc->created_at->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="flex-grow">
                            <p class="text-sm font-medium text-gray-700 mb-2">Items Solicitados:</p>
                            <ul class="list-disc pl-5 space-y-1 text-sm text-gray-800">
                                @foreach($oc->items as $item)
                                    <li>
                                        <span class="font-medium">{{ $item->insumo->nombre }}</span> &mdash; {{ $item->cantidad }} {{ $item->insumo->unidad_de_medida }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <form method="POST" action="{{ route('proveedor.oc.reject', $oc) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-5 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-red-700 transition-all duration-200 transform hover:scale-105">
                                    Rechazar
                                </button>
                            </form>
                            <form method="POST" action="{{ route('proveedor.oc.confirm', $oc) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-5 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-green-700 transition-all duration-200 transform hover:scale-105">
                                    Confirmar Pedido
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white/40 backdrop-blur-lg border border-white/20 rounded-2xl shadow-lg p-6 text-center text-gray-600">
                    <p>¡Todo en orden! No tienes órdenes de compra pendientes.</p>
                </div>
            @endforelse
        </div>
    </section>

    <section>
        <h2 class="text-2xl font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-300">
            Órdenes Confirmadas (En Preparación)
        </h2>
        
        <div class="bg-white/40 backdrop-blur-lg border border-white/20 rounded-2xl shadow-lg overflow-hidden">
            <table class="min-w-full divide-y divide-white/20">
                <thead class="bg-white/30">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">OC #</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Fecha Emisión</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Entrega Esperada</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Total Items</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($ocsConfirmadas as $oc)
                        <tr class="hover:bg-white/10 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{ $oc->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ optional($oc->fecha_emision)->format('d/m/Y') ?? $oc->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ optional($oc->fecha_entrega_esperada)->format('d/m/Y') ?? '--' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $oc->items->count() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 text-center">
                                No tienes órdenes confirmadas en preparación.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@endsection