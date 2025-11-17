<div>
    {{-- Mensaje de Éxito --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Mensaje de Error --}}
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="addToCart">
        {{-- Selector de Variante --}}
        <div class="mb-4">
            <label for="variante" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Selecciona una opción:
            </label>
            <select 
                id="variante" 
                wire:model.live="selectedVariantId" 
                class="mt-1 block w-full py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
            >
                @foreach ($variantes as $variante)
                    <option value="{{ $variante->id }}">
                        {{ $variante->descripcion }}
                    </option>
                @endforeach
            </select>
            @error('selectedVariantId') 
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
            @enderror
        </div>

        {{-- Precio Dinámico --}}
        <div class="my-4">
            <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                ${{ number_format($this->selectedVariant->precio ?? 0, 2) }}
            </span>
        </div>

        {{-- Input de Cantidad --}}
        <div class="mb-4">
            <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Cantidad:
            </label>
            <input 
                type="number" 
                id="quantity" 
                wire:model="quantity" 
                min="1" 
                class="mt-1 block w-24 py-2 px-3 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm"
            >
            @error('quantity') 
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
            @enderror
        </div>

        {{-- Botón Añadir al Carrito --}}
        <button 
            type="submit" 
            class="w-full bg-amber-500 hover:bg-amber-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors duration-200"
        >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Añadir al Carrito
        </button>
    </form>
</div>
