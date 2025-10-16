<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-pink-50 via-rose-50 to-yellow-50">
        <div class="w-full max-w-md">
            <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-2xl shadow-xl p-8">
                <div class="flex items-center space-x-4">
                    <img class="h-14 w-14" src="/favicon.ico" alt="Pastelería">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Bienvenido a Pastelería</h2>
                        <p class="text-sm text-gray-600">Inicia sesión</p>
                    </div>
                </div>

                <x-auth-session-status class="mt-4" :status="session('status')" />

                <form class="mt-6 space-y-5" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                        <div class="mt-1">
                            <x-text-input id="email" name="email" type="email" autocomplete="username" required :value="old('email')" class="block w-full px-4 py-2 rounded-lg border border-gray-200 shadow-sm focus:ring-2 focus:ring-rose-300 focus:border-rose-300" placeholder="tucorreo@ejemplo.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <div class="mt-1">
                            <x-text-input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full px-4 py-2 rounded-lg border border-gray-200 shadow-sm focus:ring-2 focus:ring-rose-300 focus:border-rose-300" placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center text-sm text-gray-700">
                            <input type="checkbox" name="remember" class="h-4 w-4 text-rose-500 focus:ring-rose-400 border-gray-300 rounded"> <span class="ml-2">Recuérdame</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-rose-600 hover:underline" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                        @endif
                    </div>

                    <div>
                        <x-primary-button class="w-full py-2 text-sm">{{ __('Iniciar sesión') }}</x-primary-button>
                    </div>

                    <p class="text-center text-sm text-gray-600">¿No tienes cuenta? <a href="{{ route('register') }}" class="text-rose-600 font-medium hover:underline">Regístrate</a></p>
                </form>
            </div>

            <div class="mt-6 text-center text-sm text-gray-500">¿Necesitas ayuda? Contáctanos en <a href="mailto:soporte@pasteleria.test" class="text-rose-600 hover:underline">soporte@pasteleria.test</a></div>
        </div>
    </div>
</x-guest-layout>
