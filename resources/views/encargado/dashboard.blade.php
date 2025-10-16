@extends('layouts.encargado')

@section('title', 'Panel Encargado')
@section('heading', 'Panel del Encargado')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 bg-white rounded shadow transform transition hover:-translate-y-1 hover:scale-105 duration-300">
            <h3 class="font-semibold text-pink-600">Compras</h3>
            <p class="text-sm text-gray-500">Gestiona órdenes de compra y proveedores.</p>
            <a class="mt-3 inline-block text-accent text-pink-500 font-medium" href="{{ route('encargado.compras') }}">Ir »</a>
        </div>
        <div class="p-4 bg-white rounded shadow transform transition hover:-translate-y-1 hover:scale-105 duration-300">
            <h3 class="font-semibold text-amber-500">Proveedores</h3>
            <p class="text-sm text-gray-500">Lista y contactos de proveedores.</p>
            <a class="mt-3 inline-block text-accent text-amber-500 font-medium" href="{{ route('encargado.proveedores') }}">Ir »</a>
        </div>
        <div class="p-4 bg-white rounded shadow transform transition hover:-translate-y-1 hover:scale-105 duration-300">
            <h3 class="font-semibold text-green-500">Producción</h3>
            <p class="text-sm text-gray-500">Control de órdenes y recetas.</p>
            <a class="mt-3 inline-block text-accent text-green-500 font-medium" href="{{ route('encargado.produccion') }}">Ir »</a>
        </div>
        <div class="p-4 bg-white rounded shadow transform transition hover:-translate-y-1 hover:scale-105 duration-300">
            <h3 class="font-semibold text-purple-500">Stock</h3>
            <p class="text-sm text-gray-500">Inventario y alertas de stock.</p>
            <a class="mt-3 inline-block text-accent text-purple-500 font-medium" href="{{ route('encargado.stock') }}">Ir »</a>
        </div>
    </div>
@endsection
