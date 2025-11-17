<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 text-center mb-8">Tu Carrito de Compras</h1>

    @if ($cartItems->isEmpty())
        <div class="text-center">
            <p class="text-gray-500 dark:text-gray-400 mb-4">Tu carrito está vacío.</p>
            <a href="{{ route('catalogo.index') }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-500 dark:hover:text-amber-400 font-medium">
                Volver al catálogo
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Lista de Items -->
            <div class="md:col-span-2">
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($cartItems as $item)
                        <li class="flex py-6">
                            <!-- Imagen del Producto -->
                            <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 dark:border-gray-700">
                                <img src="{{ $item->attributes->image ?? asset('images/producto-default.jpg') }}" 
                                     alt="{{ $item->name }}" 
                                     class="h-full w-full object-cover object-center">
                            </div>

                            <!-- Información del Item -->
                            <div class="ml-4 flex flex-1 flex-col">
                                <div>
                                    <div class="flex justify-between text-base font-medium text-gray-900 dark:text-gray-100">
                                        <h3>{{ $item->name }}</h3>
                                        <p class="ml-4">${{ number_format($item->getPriceSum(), 2) }}</p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item->attributes->variant_name }}</p>
                                </div>
                                
                                <!-- Controles de Cantidad y Eliminar -->
                                <div class="flex flex-1 items-end justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        <button 
                                            wire:click="decreaseQuantity('{{ $item->id }}')" 
                                            class="px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        >
                                            -
                                        </button>
                                        <span class="px-2 text-gray-900 dark:text-gray-100">{{ $item->quantity }}</span>
                                        <button 
                                            wire:click="increaseQuantity('{{ $item->id }}')" 
                                            class="px-2 py-0.5 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        >
                                            +
                                        </button>
                                    </div>

                                    <button 
                                        wire:click="removeItem('{{ $item->id }}')" 
                                        class="font-medium text-red-600 hover:text-red-800 dark:text-red-500 dark:hover:text-red-400"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Resumen del Pedido -->
            <div class="border-t border-gray-200 dark:border-gray-700 md:border-0 pt-8 md:pt-0">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Resumen del Pedido</h2>
                    
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">Subtotal</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">${{ number_format($subtotal, 2) }}</dd>
                        </div>
                        <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dt class="text-base font-medium text-gray-900 dark:text-gray-100">Total</dt>
                            <dd class="text-base font-medium text-gray-900 dark:text-gray-100">${{ number_format($total, 2) }}</dd>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a 
                            href="{{ route('checkout.index') }}" 
                            class="w-full flex items-center justify-center rounded-md border border-transparent bg-amber-500 hover:bg-amber-600 px-6 py-3 text-base font-medium text-white shadow-sm transition-colors duration-200"
                        >
                            Continuar al Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Indicador de Carga -->
        <div wire:loading class="fixed inset-0 bg-white dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-center space-x-3">
                    <svg class="animate-spin h-6 w-6 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Actualizando carrito...</span>
                </div>
            </div>
        </div>
    @endif
</div>
