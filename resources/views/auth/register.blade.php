<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-pink-50 via-rose-50 to-yellow-50">
        <div class="w-full max-w-md">
            <div class="bg-white/80 backdrop-blur-md border border-white/60 rounded-2xl shadow-xl p-8">
                <div class="flex items-center space-x-4">
                    <img class="h-14 w-14" src="/favicon.ico" alt="Pastelería">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Crea tu cuenta</h2>
                        <p class="text-sm text-gray-600">Regístrate para comenzar a administrar tu pastelería</p>
                    </div>
                </div>

                <form class="mt-6 space-y-5" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                        <div class="mt-1">
                            <x-text-input id="name" name="name" type="text" required :value="old('name')" class="block w-full px-4 py-2 rounded-lg border border-gray-200 shadow-sm focus:ring-2 focus:ring-rose-300 focus:border-rose-300" placeholder="Tu nombre" />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                        <div class="mt-1">
                            <x-text-input id="email" name="email" type="email" required :value="old('email')" class="block w-full px-4 py-2 rounded-lg border border-gray-200 shadow-sm focus:ring-2 focus:ring-rose-300 focus:border-rose-300" placeholder="tucorreo@ejemplo.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                        <div class="mt-1">
                            <x-text-input id="password" name="password" type="password" required class="block w-full px-4 py-2 rounded-lg border border-gray-200 shadow-sm focus:ring-2 focus:ring-rose-300 focus:border-rose-300" placeholder="Mínimo 8 caracteres" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                        <div class="mt-1">
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" required class="block w-full px-4 py-2 rounded-lg border border-gray-200 shadow-sm focus:ring-2 focus:ring-rose-300 focus:border-rose-300" placeholder="Repite tu contraseña" />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div>
                        <x-primary-button class="w-full py-2 text-sm">{{ __('Registrarse') }}</x-primary-button>
                    </div>

                    <p class="text-center text-sm text-gray-600">¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-rose-600 font-medium hover:underline">Inicia sesión</a></p>
                </form>
            </div>

            <div class="mt-6 text-center text-sm text-gray-500">Al registrarte aceptas nuestros <a href="#" class="text-rose-600 hover:underline">Términos y Condiciones</a></div>
        </div>
    </div>
</x-guest-layout>
