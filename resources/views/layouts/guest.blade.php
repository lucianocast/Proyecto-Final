<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gradient-to-b from-pink-50 via-rose-50 to-yellow-50 dark:from-gray-800 dark:via-gray-900 dark:to-black">
        <div class="min-h-screen relative flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8">
            <div class="absolute inset-0 overflow-hidden pointer-events-none z-0" aria-hidden="true">
                <svg class="absolute -right-16 -top-8 opacity-12 transform scale-75" width="300" height="300" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="g1" x1="0%" x2="100%" y1="0%" y2="100%">
                            <stop offset="0%" stop-color="#FDE68A" />
                            <stop offset="100%" stop-color="#FBCFE8" />
                        </linearGradient>
                    </defs>
                    <circle cx="60" cy="60" r="70" fill="url(#g1)" />
                </svg>
            </div>

            <div class="w-full max-w-xl relative z-10 pointer-events-auto">
                <div class="flex justify-center mb-4">
                    <a href="/" class="inline-flex items-center">
                        <x-application-logo class="w-14 h-14 text-gray-700 dark:text-gray-200" />
                    </a>
                </div>

                <div class="bg-white/85 dark:bg-gray-800/80 backdrop-blur-sm border border-white/50 dark:border-gray-700/30 rounded-2xl shadow-lg px-8 py-8 max-h-[520px] relative z-20 pointer-events-auto">
                    {{ $slot }}
                </div>

                <div class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">¿Problemas para iniciar sesión? <a href="mailto:soporte@pasteleria.test" class="text-rose-600 hover:underline">Contáctanos</a></div>
            </div>
        </div>
    </body>
</html>
