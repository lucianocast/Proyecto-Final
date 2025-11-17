<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Título del Catálogo -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    Nuestro Catálogo
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Descubre nuestros deliciosos productos de pastelería
                </p>
            </div>

            @if ($productos->isEmpty())
                <!-- Mensaje cuando no hay productos -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 dark:text-gray-400 text-center">
                        No hay productos disponibles en este momento.
                    </p>
                </div>
            @else
                <!-- Grid de Productos -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($productos as $producto)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
                            <!-- Imagen del Producto -->
                            <div class="aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
                                <img 
                                    src="{{ $producto->imagen_url ?? asset('images/producto-default.jpg') }}" 
                                    alt="{{ $producto->nombre }}"
                                    class="w-full h-full object-cover"
                                >
                            </div>

                            <!-- Información del Producto -->
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $producto->nombre }}
                                </h3>

                                @if ($producto->descripcion)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                        {{ $producto->descripcion }}
                                    </p>
                                @endif

                                <!-- Variantes de Precio -->
                                @if ($producto->variantes->isNotEmpty())
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Desde:</p>
                                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-500">
                                            ${{ number_format($producto->variantes->min('precio'), 2) }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Botón Ver Más -->
                                <a 
                                    href="{{ route('catalogo.show', $producto) }}"
                                    class="block w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200 text-center"
                                >
                                    Ver Más
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-8">
                    {{ $productos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>
