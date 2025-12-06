@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-4">
            <svg class="mx-auto h-16 w-16 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Pago Pendiente</h1>
        <p class="text-gray-600 mb-6">Tu pago está siendo procesado. Te notificaremos cuando esté confirmado.</p>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-yellow-800">
                <strong>Nota:</strong> Algunos métodos de pago pueden tardar hasta 48 horas en confirmarse.
            </p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('catalogo.index') }}" class="block w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
                Volver al Catálogo
            </a>
        </div>
    </div>
</div>
@endsection
