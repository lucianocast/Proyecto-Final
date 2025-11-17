<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Botón de Regreso -->
            <div class="mb-6">
                <a href="{{ route('catalogo.index') }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al Catálogo
                </a>
            </div>

            <!-- Grid Principal: 2 Columnas en Desktop -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Columna Izquierda: Galería de Imágenes -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <div class="aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <img 
                            src="{{ $producto->imagen_url ?? asset('images/producto-default.jpg') }}" 
                            alt="{{ $producto->nombre }}"
                            class="w-full h-full object-cover"
                        >
                    </div>
                    
                    <!-- Thumbnails (si hay múltiples imágenes en el futuro) -->
                    {{-- 
                    @if($producto->imagenes && $producto->imagenes->count() > 1)
                        <div class="p-4 grid grid-cols-4 gap-2">
                            @foreach($producto->imagenes as $imagen)
                                <div class="aspect-square overflow-hidden rounded border-2 border-gray-300 hover:border-amber-500 cursor-pointer">
                                    <img src="{{ $imagen->url }}" alt="Thumbnail" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                    --}}
                </div>

                <!-- Columna Derecha: Información y Formulario -->
                <div class="space-y-6">
                    
                    <!-- Título y Categoría -->
                    <div>
                        @if($producto->categoria)
                            <p class="text-sm text-amber-600 dark:text-amber-500 font-semibold uppercase tracking-wide">
                                {{ $producto->categoria->nombre }}
                            </p>
                        @endif
                        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-gray-100 mt-2">
                            {{ $producto->nombre }}
                        </h1>
                    </div>

                    <!-- Descripción -->
                    @if($producto->descripcion)
                        <div class="prose dark:prose-invert max-w-none">
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                {{ $producto->descripcion }}
                            </p>
                        </div>
                    @endif

                    <!-- Etiquetas -->
                    @if($producto->etiquetas && is_array($producto->etiquetas) && count($producto->etiquetas) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($producto->etiquetas as $etiqueta)
                                <span class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-full">
                                    {{ $etiqueta }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Componente Livewire: Añadir al Carrito -->
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                        <livewire:frontend.add-to-cart-form :producto="$producto" />
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
