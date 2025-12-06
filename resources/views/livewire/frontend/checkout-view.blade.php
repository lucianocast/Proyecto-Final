<div class="min-h-screen bg-orange-50">
    <!-- Header del Checkout -->
    <div class="bg-orange-200 shadow-sm">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-4xl font-bold text-orange-700 text-center">
                🎂 Finalizar Pedido
            </h1>
            <p class="mt-2 text-orange-600 text-center">
                Completa los datos para confirmar tu pedido
            </p>
        </div>
    </div>

    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if ($errors->has('general'))
            <div class="bg-red-50 border-2 border-red-300 text-red-700 px-6 py-4 rounded-lg mb-6">
                {{ $errors->first('general') }}
            </div>
        @endif

        <form wire:submit.prevent="saveOrder">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna Izquierda: Formulario de Datos de Entrega -->
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-lg rounded-2xl p-8">
                        <h2 class="text-2xl font-bold text-orange-700 mb-6">Datos de Entrega</h2>

                        <!-- Forma de Entrega -->
                        <div class="mb-6">
                            <label class="block text-gray-800 font-semibold mb-3">
                                Forma de Entrega
                            </label>
                            <div class="flex gap-4">
                                <label class="flex items-center bg-orange-50 px-4 py-3 rounded-lg border-2 border-orange-200 hover:border-orange-400 cursor-pointer transition-colors">
                                    <input type="radio" name="forma_entrega_radio" wire:model.live="forma_entrega" value="retiro" class="text-orange-500 focus:ring-orange-400">
                                    <span class="ml-3 font-medium text-gray-700">🏪 Retiro en local</span>
                                </label>
                                <label class="flex items-center bg-orange-50 px-4 py-3 rounded-lg border-2 border-orange-200 hover:border-orange-400 cursor-pointer transition-colors">
                                    <input type="radio" name="forma_entrega_radio" wire:model.live="forma_entrega" value="envio" class="text-orange-500 focus:ring-orange-400">
                                    <span class="ml-3 font-medium text-gray-700">🚚 Envío a domicilio</span>
                                </label>
                            </div>
                        @error('forma_entrega')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                        <!-- Dirección de Envío (solo si eligió "envio") -->
                        @if ($forma_entrega === 'envio')
                            <div class="mb-6">
                                <label class="block text-gray-800 font-semibold mb-3">
                                    Dirección de Envío
                                </label>
                                <input 
                                    type="text" 
                                    wire:model="direccion_envio" 
                                    class="w-full border-2 border-orange-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                    placeholder="Calle, número, colonia, ciudad"
                                >
                            @error('direccion_envio')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                        <!-- Fecha de Entrega -->
                        <div class="mb-6">
                            <label class="block text-gray-800 font-semibold mb-3">
                                Fecha de Entrega
                            </label>
                            <input 
                                type="datetime-local" 
                                wire:model="fecha_entrega" 
                                class="w-full border-2 border-orange-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                            >
                        @error('fecha_entrega')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                        <!-- Observaciones -->
                        <div class="mb-6">
                            <label class="block text-gray-800 font-semibold mb-3">
                                Observaciones (opcional)
                            </label>
                            <textarea 
                                wire:model="observaciones" 
                                rows="4"
                                class="w-full border-2 border-orange-200 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400"
                                placeholder="Ej: Sin gluten, decoración especial, etc."
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Resumen del Pedido -->
                <div class="lg:col-span-1">
                    <div class="bg-white shadow-lg rounded-2xl p-8 lg:sticky lg:top-8">
                        <h2 class="text-2xl font-bold text-orange-700 mb-6">Resumen del Pedido</h2>

                        <div class="space-y-4 mb-6">
                            @foreach ($cartItems as $item)
                                <div class="flex items-start gap-3 pb-4 border-b border-orange-100">
                                    <div class="flex-shrink-0">
                                        @if(isset($item->attributes->image) && $item->attributes->image !== 'default.jpg')
                                            <img src="{{ asset('storage/' . $item->attributes->image) }}" 
                                                 alt="{{ $item->name }}" 
                                                 class="w-16 h-16 object-cover rounded-lg border-2 border-orange-200">
                                        @else
                                            <div class="w-16 h-16 bg-orange-50 rounded-lg flex items-center justify-center border-2 border-orange-200">
                                                <svg class="h-8 w-8 text-orange-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $item->attributes->variant_name }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Cantidad: {{ $item->quantity }} × ${{ number_format($item->price, 2) }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="font-bold text-orange-600">${{ number_format($item->getPriceSum(), 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t-2 border-orange-200 pt-6">
                            <div class="flex justify-between items-center mb-6">
                                <span class="text-xl font-bold text-gray-800">Total</span>
                                <span class="text-3xl font-bold text-orange-600">${{ number_format($total, 2) }}</span>
                            </div>

                            <button 
                                type="submit" 
                                class="w-full bg-orange-400 hover:bg-orange-500 text-white py-4 rounded-full font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-200"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove class="flex items-center justify-center">
                                    Confirmar Pedido
                                    <svg class="w-6 h-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </span>
                                <span wire:loading class="flex items-center justify-center">
                                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Procesando...
                                </span>
                            </button>

                            <a href="{{ route('cart.index') }}" 
                               class="block text-center mt-4 text-orange-600 hover:text-orange-800 font-medium">
                                ← Volver al Carrito
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
