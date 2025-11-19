<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirige al usuario a la página de autenticación de Google.
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Maneja el callback de Google después de la autenticación.
     */
    public function handleProviderCallback()
    {
        try {
            // Obtener los datos del usuario de Google
            $googleUser = Socialite::driver('google')->user();

            // Buscar usuario existente por email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Si el usuario existe pero no tiene google_id, actualizar
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                    ]);
                }
                // No crear perfil de Cliente (asume que ya existe)
            } else {
                // Crear nuevo usuario
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(Str::random(24)), // Contraseña aleatoria
                    'email_verified_at' => now(), // Auto-verificar email de Google
                ]);

                // Crear perfil de Cliente asociado al nuevo usuario
                Cliente::create([
                    'user_id' => $user->id,
                    'nombre' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'telefono' => null, // Para completar después
                    'direccion' => null, // Para completar después
                ]);
            }

            // Autenticar al usuario
            Auth::login($user, true);

            // Redirigir al dashboard
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            // Manejar errores de autenticación
            return redirect()->route('login')
                           ->with('error', 'Error al autenticar con Google. Por favor, intenta de nuevo.');
        }
    }
}
