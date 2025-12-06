<x-guest-layout>
<div class="min-h-screen bg-gray-50">
    <!-- Header Principal con Logo/Marca -->
    <div class="bg-gradient-to-r from-orange-200 via-orange-100 to-peach-100 shadow-sm">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
            <h1 class="text-5xl font-bold text-orange-800 mb-2" style="font-family: 'Brush Script MT', cursive;">
                ContuCocina
            </h1>
            <p class="text-sm uppercase tracking-widest text-orange-600 font-light">
                Pastelería
            </p>
        </div>
    </div>

    <!-- Navegación Superior -->
    <div class="bg-orange-50 border-b border-orange-200">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex justify-center items-center space-x-8 py-4">
                <a href="{{ route('catalogo.index') }}" class="text-orange-600 hover:text-orange-800 font-medium transition-colors">
                    Bienvenido
                </a>
                <a href="{{ route('cart.index') }}" class="text-orange-600 hover:text-orange-800 font-medium transition-colors relative inline-flex items-center">
                    🛒 
                    <livewire:frontend.cart-counter />
                </a>
            </nav>
        </div>
    </div>

    <!-- Contenedor Principal del Catálogo -->
    <div class="bg-orange-50 py-8 px-4">
        <div class="max-w-screen-xl mx-auto">
            
            <!-- Botón CTA -->
            <div class="text-center mb-12">
                <button class="bg-orange-300 hover:bg-orange-400 text-orange-800 font-medium px-8 py-3 rounded-full shadow-md transition-colors duration-200">
                    Descrube nuestros postres
                </button>
            </div>

            <!-- Filtros de Categorías (Opcional) -->
            @if(isset($categorias) && $categorias->isNotEmpty())
            <div class="mb-8">
                <div class="flex justify-center flex-wrap gap-3">
                    <a href="{{ route('catalogo.index') }}" 
                       class="px-6 py-2 rounded-full {{ !request('categoria') ? 'bg-orange-400 text-white' : 'bg-white text-orange-700 hover:bg-orange-200' }} transition-colors duration-200 shadow-sm">
                        Todas
                    </a>
                    @foreach($categorias as $categoria)
                    <a href="{{ route('catalogo.index', ['categoria' => $categoria->id]) }}" 
                       class="px-6 py-2 rounded-full {{ request('categoria') == $categoria->id ? 'bg-orange-400 text-white' : 'bg-white text-orange-700 hover:bg-orange-200' }} transition-colors duration-200 shadow-sm">
                        {{ $categoria->nombre }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if ($productos->isEmpty())
                <!-- Mensaje cuando no hay productos -->
                <div class="bg-white rounded-2xl shadow-lg p-16 text-center">
                    <svg class="mx-auto h-20 w-20 text-orange-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="text-orange-600 text-xl font-medium">
                        No hay productos disponibles en este momento.
                    </p>
                </div>
            @else
                <!-- Grid de Productos -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($productos as $producto)
                        <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-shadow duration-300 overflow-hidden">
                            <!-- Imagen del Producto -->
                            <div class="aspect-square overflow-hidden bg-gray-100">
                                @if($producto->imagen_url)
                                    <img 
                                        src="{{ asset('storage/' . $producto->imagen_url) }}" 
                                        alt="{{ $producto->nombre }}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                    >
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-orange-50">
                                        <svg class="h-32 w-32 text-orange-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Información del Producto -->
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">
                                    {{ $producto->nombre }}
                                </h3>

                                @if ($producto->descripcion)
                                    <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                        {{ $producto->descripcion }}
                                    </p>
                                @endif

                                <!-- Precio y Componente Livewire -->
                                <livewire:frontend.add-to-cart-form :producto="$producto" :key="$producto->id" />
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-12">
                    {{ $productos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
</x-guest-layout>
