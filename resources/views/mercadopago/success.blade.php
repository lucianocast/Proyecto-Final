@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-4">
            <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-2">¡Pago Exitoso!</h1>
        <p class="text-gray-600 mb-6">Tu pago ha sido procesado correctamente.</p>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
            <p class="text-sm text-gray-600 mb-1"><strong>ID de Pago:</strong> {{ $paymentId }}</p>
            <p class="text-sm text-gray-600 mb-1"><strong>Estado:</strong> {{ $status }}</p>
            <p class="text-sm text-gray-600"><strong>Referencia:</strong> Pedido #{{ $externalReference }}</p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('catalogo.index') }}" class="block w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                Volver al Catálogo
            </a>
        </div>
    </div>
</div>
@endsection
