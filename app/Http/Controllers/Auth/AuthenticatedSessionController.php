<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirección según rol (prioridad)
        $user = Auth::user();
        if ($user) {
            if (method_exists($user, 'role') || true) {
                $role = $user->role ?? null;
                // Prioridad: administrador -> encargado -> vendedor -> proveedor -> cliente
                if ($role === 'administrador' || $role === 'admin') {
                    return redirect()->intended(route('admin.dashboard', absolute: false));
                }
                if ($role === 'encargado') {
                    return redirect()->intended(route('encargado.dashboard', absolute: false));
                }
                if ($role === 'vendedor') {
                    return redirect()->intended(route('vendedor.dashboard', absolute: false));
                }
                if ($role === 'proveedor') {
                    return redirect()->intended(route('proveedor.dashboard', absolute: false));
                }
                if ($role === 'cliente') {
                    return redirect()->intended(route('cliente.dashboard', absolute: false));
                }
            }
        }

        // Fallback al dashboard genérico
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
