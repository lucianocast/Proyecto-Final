<div class="ml-4 flow-root lg:ml-6">
    <a href="{{ route('cart.index') }}" class="group -m-2 p-2 flex items-center">
        <!-- Icono del Carrito -->
        <svg class="h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.57 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
        </svg>

        <!-- Badge con el Contador -->
        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-amber-100 dark:bg-amber-900 rounded-full px-2 py-0.5 group-hover:text-gray-800 dark:group-hover:text-gray-200">
            {{ $count }}
        </span>

        <span class="sr-only">items in cart, view bag</span>
    </a>
</div>
