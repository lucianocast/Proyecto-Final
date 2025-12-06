<div class="min-h-screen bg-orange-50">
    <!-- Header del Carrito -->
    <div class="bg-orange-200 shadow-sm">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-4xl font-bold text-orange-700 text-center">
                🛒 Tu Carrito de Compras
            </h1>
            <p class="mt-2 text-orange-600 text-center">
                Revisa tus productos antes de continuar
            </p>
        </div>
    </div>

    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($cartItems->isEmpty())
            <div class="bg-white rounded-2xl shadow-lg p-16 text-center">
                <svg class="mx-auto h-24 w-24 text-orange-300 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="text-gray-600 text-xl mb-6">Tu carrito está vacío.</p>
                <a href="{{ route('catalogo.index') }}" class="inline-block bg-orange-400 hover:bg-orange-500 text-white font-semibold px-8 py-3 rounded-full shadow-md transition-colors duration-200">
                    Volver al Catálogo
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Lista de Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="divide-y divide-orange-100">
                            @foreach ($cartItems as $item)
                                <div class="flex p-6 hover:bg-orange-50 transition-colors duration-200">
                                    <!-- Imagen del Producto -->
                                    <div class="h-28 w-28 flex-shrink-0 overflow-hidden rounded-lg border-2 border-orange-200">
                                        @php
                                            $imagePath = $item->attributes['image'] ?? $item->attributes->image ?? null;
                                        @endphp
                                        @if($imagePath && $imagePath !== 'default.jpg')
                                            <img 
                                                src="{{ asset('storage/' . $imagePath) }}" 
                                                alt="{{ $item->name }}"
                                                class="h-full w-full object-cover object-center"
                                                onerror="this.parentElement.innerHTML='<div class=\'h-full w-full flex items-center justify-center bg-orange-50\'><svg class=\'h-16 w-16 text-orange-200\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\' /></svg></div>'"
                                            >
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-orange-50">
                                                <svg class="h-16 w-16 text-orange-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Información del Item -->
                                    <div class="ml-6 flex flex-1 flex-col justify-between">
                                        <div>
                                            <div class="flex justify-between">
                                                <h3 class="text-lg font-bold text-gray-800">{{ $item->name }}</h3>
                                                <p class="ml-4 text-lg font-bold text-orange-600">${{ number_format($item->getPriceSum(), 2) }}</p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-600">{{ $item->attributes->variant_name }}</p>
                                            <p class="mt-1 text-xs text-gray-500">${{ number_format($item->price, 2) }} c/u</p>
                                        </div>
                                        
                                        <!-- Controles de Cantidad y Eliminar -->
                                        <div class="flex items-center justify-between mt-4">
                                            <div class="flex items-center space-x-3 bg-orange-50 rounded-lg px-3 py-2">
                                                <button 
                                                    wire:click="decreaseQuantity('{{ $item->id }}')" 
                                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white border-2 border-orange-300 hover:bg-orange-100 text-orange-600 font-bold transition-colors duration-200"
                                                >
                                                    -
                                                </button>
                                                <span class="px-3 font-semibold text-gray-800 min-w-[2rem] text-center">{{ $item->quantity }}</span>
                                                <button 
                                                    wire:click="increaseQuantity('{{ $item->id }}')" 
                                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white border-2 border-orange-300 hover:bg-orange-100 text-orange-600 font-bold transition-colors duration-200"
                                                >
                                                    +
                                                </button>
                                            </div>

                                            <button 
                                                wire:click="removeItem('{{ $item->id }}')" 
                                                class="flex items-center space-x-1 font-medium text-red-500 hover:text-red-700 transition-colors duration-200"
                                            >
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                <span>Eliminar</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Resumen del Pedido -->
                <div class="lg:sticky lg:top-8">
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-orange-700 mb-6">Resumen del Pedido</h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-orange-100">
                                <dt class="text-base text-gray-600">Subtotal</dt>
                                <dd class="text-base font-semibold text-gray-800">${{ number_format($subtotal, 2) }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-3 border-b border-orange-100">
                                <dt class="text-base text-gray-600">Items</dt>
                                <dd class="text-base font-semibold text-gray-800">{{ $cartItems->count() }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-4 border-t-2 border-orange-200">
                                <dt class="text-xl font-bold text-gray-800">Total</dt>
                                <dd class="text-2xl font-bold text-orange-600">${{ number_format($total, 2) }}</dd>
                            </div>
                        </div>

                        <div class="mt-8 space-y-3">
                            <a 
                                href="{{ route('checkout.index') }}" 
                                class="w-full flex items-center justify-center rounded-full bg-orange-400 hover:bg-orange-500 px-8 py-4 text-lg font-bold text-white shadow-lg hover:shadow-xl transition-all duration-200"
                            >
                                Continuar al Checkout
                                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <a 
                                href="{{ route('catalogo.index') }}" 
                                class="w-full flex items-center justify-center rounded-full bg-white border-2 border-orange-300 hover:bg-orange-50 px-8 py-3 text-base font-semibold text-orange-600 transition-colors duration-200"
                            >
                                Seguir Comprando
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Indicador de Carga -->
            <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl shadow-2xl p-8">
                    <div class="flex items-center space-x-4">
                        <svg class="animate-spin h-8 w-8 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-xl font-semibold text-gray-800">Actualizando carrito...</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
