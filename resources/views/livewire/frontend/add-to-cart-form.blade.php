<div>
    {{-- Mensaje de Éxito --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-300 text-green-700 px-3 py-2 rounded-lg mb-3 text-sm" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Mensaje de Error --}}
    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-300 text-red-700 px-3 py-2 rounded-lg mb-3 text-sm" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="addToCart" class="space-y-3">
        {{-- Selector de Variante --}}
        @if($variantes->count() > 1)
        <div>
            <label for="variante" class="block text-sm font-medium text-orange-700 mb-1">
                Selecciona una opción:
            </label>
            <select 
                id="variante" 
                wire:model.live="selectedVariantId" 
                class="block w-full py-2 px-3 border border-orange-200 bg-white text-gray-900 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 text-sm"
            >
                @foreach ($variantes as $variante)
                    <option value="{{ $variante->id }}">
                        {{ $variante->descripcion }} - ${{ number_format($variante->precio, 2) }}
                    </option>
                @endforeach
            </select>
            @error('selectedVariantId') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>
        @endif

        {{-- Input de Cantidad --}}
        <div class="flex items-center gap-3">
            <label for="quantity" class="text-sm font-medium text-orange-700">
                Cantidad:
            </label>
            <input 
                type="number" 
                id="quantity" 
                wire:model="quantity" 
                min="1" 
                class="w-20 py-2 px-3 border border-orange-200 bg-white text-gray-900 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 text-sm text-center"
            >
            @error('quantity') 
                <span class="text-red-500 text-xs">{{ $message }}</span> 
            @enderror
        </div>

        {{-- Botón Añadir al Carrito --}}
        <button 
            type="submit" 
            class="w-full bg-orange-400 hover:bg-orange-500 border border-transparent rounded-lg py-3 px-6 flex items-center justify-center text-base font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-colors duration-200 shadow-md hover:shadow-lg"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Añadir al Carrito
        </button>
    </form>
</div>
