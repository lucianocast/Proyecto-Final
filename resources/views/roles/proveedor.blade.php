@extends('layouts.proveedor')

@section('title', 'Panel Proveedor')

@section('content')
    @php
        $user = auth()->user();
        $proveedor = $user->proveedor ?? null;
    @endphp

    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold">Bienvenido, {{ $proveedor->nombre_empresa ?? $user->name }}</h2>
        <p class="text-sm text-gray-600 mb-4">Aquí puedes revisar y gestionar las órdenes que te han solicitado.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('proveedor.dashboard') }}" class="block p-4 bg-indigo-50 hover:bg-indigo-100 rounded border">
                <div class="font-medium">Ver Panel</div>
                <div class="text-xs text-gray-500">Revisa órdenes pendientes y confirmadas</div>
            </a>

            <a href="{{ route('profile.edit') }}" class="block p-4 bg-gray-50 hover:bg-gray-100 rounded border">
                <div class="font-medium">Editar Perfil</div>
                <div class="text-xs text-gray-500">Actualiza tus datos de contacto</div>
            </a>

            <div class="p-4 bg-green-50 rounded border">
                <div class="font-medium">Catálogo</div>
                <div class="text-xs text-gray-500">Gestiona los insumos que ofreces (desde el panel encargado)</div>
            </div>

            <div class="p-4 bg-yellow-50 rounded border">
                <div class="font-medium">Soporte</div>
                <div class="text-xs text-gray-500">¿Necesitas ayuda? <a href="mailto:soporte@pasteleria.test" class="text-blue-600">Contacta soporte</a></div>
            </div>
        </div>

        <div class="mt-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded">Cerrar sesión</button>
            </form>
        </div>
    </div>
@endsection
