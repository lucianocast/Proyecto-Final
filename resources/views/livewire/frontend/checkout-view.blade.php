<div class="max-w-5xl mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Finalizar Pedido</h1>

    @if ($errors->has('general'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $errors->first('general') }}
        </div>
    @endif

    <form wire:submit.prevent="saveOrder">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Columna Izquierda: Formulario de Datos de Entrega -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-4">Datos de Entrega</h2>

                    <!-- Forma de Entrega -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Forma de Entrega
                        </label>
                        <div class="flex gap-4">

                            <label class="flex items-center">
                                <input type="radio" name="forma_entrega_radio" wire:model.live="forma_entrega" value="retiro" ...>
                                <span class="ml-2">Retiro en local</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="forma_entrega_radio" wire:model.live="forma_entrega" value="envio" ...>
                                <span class="ml-2">Envío a domicilio</span>
                            </label>
                        </div>
                        @error('forma_entrega')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Dirección de Envío (solo si eligió "envio") -->
                    @if ($forma_entrega === 'envio')
                        <div class="mb-4">
                            <label class="block text-gray-700 font-medium mb-2">
                                Dirección de Envío
                            </label>
                            <input 
                                type="text" 
                                wire:model="direccion_envio" 
                                class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                                placeholder="Calle, número, colonia, ciudad"
                            >
                            @error('direccion_envio')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <!-- Fecha de Entrega -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Fecha de Entrega
                        </label>
                        <input 
                            type="datetime-local" 
                            wire:model="fecha_entrega" 
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                        >
                        @error('fecha_entrega')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Observaciones -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">
                            Observaciones (opcional)
                        </label>
                        <textarea 
                            wire:model="observaciones" 
                            rows="3"
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:ring-blue-300"
                            placeholder="Ej: Sin gluten, decoración especial, etc."
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Resumen del Pedido -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6 sticky top-4">
                    <h2 class="text-xl font-semibold mb-4">Resumen del Pedido</h2>

                    <div class="space-y-3 mb-4">
                        @foreach ($cartItems as $item)
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2">
                                    @if ($item->attributes->imagen_url)
                                        <img src="{{ $item->attributes->imagen_url }}" 
                                             alt="{{ $item->name }}" 
                                             class="w-12 h-12 object-cover rounded">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-gray-400 text-xs">Sin imagen</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium">{{ $item->name }}</p>
                                        <p class="text-gray-500 text-xs">Cantidad: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <span class="font-semibold">${{ number_format($item->getPriceSum(), 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <hr class="my-4">

                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Total</span>
                        <span class="text-green-600">${{ number_format($total, 2) }}</span>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full mt-6 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Confirmar y Pagar con Mercado Pago</span>
                        <span wire:loading>Procesando...</span>
                    </button>

                    <a href="{{ route('cart.index') }}" 
                       class="block text-center mt-4 text-gray-600 hover:text-gray-800">
                        ← Volver al Carrito
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
