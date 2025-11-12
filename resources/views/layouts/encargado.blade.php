<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Panel Encargado')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pastel theme and custom animations */
        :root{
            --pastel-pink: #ffe4ec;
            --pastel-cream: #fff7ed;
            --pastel-mint: #e6fffa;
            --accent: #f973a6; /* pastel rose */
        }

        @keyframes floaty {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
            100% { transform: translateY(0px); }
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        @keyframes fade-in { from { opacity: 0; transform: translateY(6px); } to { opacity:1; transform: translateY(0); } }

        .animate-float { animation: floaty 3.5s ease-in-out infinite; }
        .animate-shimmer { background: linear-gradient(90deg, rgba(255,255,255,0.0) 0%, rgba(255,255,255,0.25) 50%, rgba(255,255,255,0.0) 100%); background-size: 200% 100%; animation: shimmer 2.5s linear infinite; }
        .animate-fade-in { animation: fade-in .5s ease both; }

        /* subtle background gradient */
        .pastel-bg { background: radial-gradient(circle at 10% 10%, var(--pastel-pink) 0%, transparent 20%), radial-gradient(circle at 90% 90%, var(--pastel-mint) 0%, transparent 20%), var(--pastel-cream); }

        /* small cupcake icon style */
        .cupcake { width: 36px; height: 36px; }
    </style>
</head>

<body class="pastel-bg text-gray-800">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-white/80 backdrop-blur-md border-r border-gray-200 shadow-sm">
            <div class="p-4 border-b border-gray-100 flex items-center space-x-3">
                <svg class="cupcake animate-float" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M6 13c0-1.657 1.343-3 3-3h6c1.657 0 3 1.343 3 3v2a2 2 0 01-2 2H8a2 2 0 01-2-2v-2z" fill="#FDE68A"/>
                    <path d="M5 11a7 7 0 0114 0v1H5v-1z" fill="#FCA5A5"/>
                    <path d="M12 3c1.38 0 2.5 1.12 2.5 2.5S13.38 8 12 8 9.5 6.88 9.5 5.5 10.62 3 12 3z" fill="#FEE2E2"/>
                </svg>
                @auth
                    @php $role = strtolower($userRole = auth()->user()->role ?? ''); @endphp
                    <h2 class="text-lg font-semibold">Pastelería · {{ ucfirst($userRole ?: 'Usuario') }}</h2>
                @else
                    <h2 class="text-lg font-semibold">Pastelería</h2>
                @endauth
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    @auth
                        @php $role = strtolower(auth()->user()->role ?? ''); @endphp

                        @if($role === 'administrador' || $role === 'admin')
                            <li><a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200 shadow-sm hover:shadow-md">Inicio</a></li>
                            <li><a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Gestionar usuarios y roles</a></li>
                            <li><a href="{{ route('admin.sistema') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Configurar Sistema</a></li>
                            <li><a href="{{ route('admin.notificaciones') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Notificaciones y Alertas</a></li>
                            <li><a href="{{ route('admin.reportes') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Reportes y Análisis</a></li>
                            <li><a href="{{ route('admin.auditoria') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Auditoría</a></li>

                                {{-- Enlaces rápidos para gestión (opcional) --}}
                                <li><a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Panel Admin</a></li>

                        @elseif($role === 'encargado')
                            <li><a href="{{ route('encargado.dashboard') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Inicio</a></li>
                            <li><a href="{{ route('encargado.compras') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Gestionar compras</a></li>
                            <li><a href="{{ route('encargado.proveedores') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Gestionar proveedores</a></li>
                            <li><a href="{{ route('encargado.produccion') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Gestionar producción</a></li>
                            <li><a href="{{ route('encargado.stock') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Gestionar stock</a></li>
                                {{-- Nuevos enlaces rápidos solicitados --}}
                                <li><a href="{{ route('encargado.insumos.index') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Insumos</a></li>
                                <li><a href="{{ route('encargado.categorias-insumos.index') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Categorías de Insumos</a></li>

                        @elseif($role === 'vendedor')
                            <li><a href="#" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Inicio Vendedor</a></li>
                            <li><a href="#" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Gestión de ventas</a></li>

                        @elseif($role === 'proveedor')
                            <li><a href="#" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Inicio Proveedor</a></li>
                            <li><a href="#" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Mis envíos / contratos</a></li>

                        @elseif($role === 'cliente')
                            <li><a href="{{ route('cliente.dashboard') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Mi panel</a></li>
                            <li><a href="#" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Mis pedidos</a></li>

                        @else
                            <li><a href="{{ url('/') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Inicio</a></li>
                        @endif
                    @else
                        <li><a href="{{ route('login') }}" class="block px-3 py-2 rounded hover:bg-white transition-colors duration-200">Iniciar sesión</a></li>
                    @endauth
                </ul>
            </nav>
        </aside>

            <main class="flex-1 p-6">
            <header class="mb-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">@yield('heading', 'Panel')</h1>

                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="flex items-center space-x-3">
                                <!-- Avatar -->
                                <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
                                    <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                                </div>

                                <!-- Dropdown / Logout -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="ml-4 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm">Cerrar sesión</button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-blue-600">Iniciar sesión</a>
                        @endauth
                    </div>
                </div>
            </header>

            <section class="animate-fade-in">
                @yield('content')
            </section>
        </main>
    </div>
</body>
</html>
