<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Pastelería</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- Estilos Generales y Fondo Animado (Consistente con welcome.blade.php) --- */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            color: #333;
            background: linear-gradient(135deg, #fde7e7, #fef8e8, #e6f9f5);
            background-size: 400% 400%;
            animation: gradientAnimation 18s ease infinite;
            height: 100vh;
            display: flex; /* Usa Flexbox para centrar el contenido */
            justify-content: center;
            align-items: center;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- Tarjeta de Login con Efecto "Glass" --- */
        .login-card {
            background: rgba(255, 255, 255, 0.3);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            width: 100%;
            max-width: 420px;
            box-sizing: border-box; /* Asegura que el padding no afecte el ancho total */
            animation: contentFadeInUp 1s ease-out;
        }
        
        @keyframes contentFadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- Estilos del Formulario --- */
        .login-card h2 {
            text-align: center;
            font-weight: 600;
            font-size: 28px;
            margin-top: 0;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .login-card p {
            text-align: center;
            margin-bottom: 30px;
            font-size: 15px;
            color: #555;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
            color: #34495e;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            background-color: rgba(255, 255, 255, 0.7);
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
        }

        .input-group input:focus {
            outline: none;
            border-color: #f79a9a; /* Un color suave del tema */
            box-shadow: 0 0 0 3px rgba(253, 231, 231, 0.6);
        }

        /* --- Opciones (Recuérdame y Olvidé contraseña) --- */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .form-options .remember-me {
            display: flex;
            align-items: center;
        }

        .form-options .remember-me input {
            margin-right: 8px;
        }

        .form-options a {
            color: #e74c3c;
            text-decoration: none;
            font-weight: 500;
            transition: text-decoration 0.3s ease;
        }

        .form-options a:hover {
            text-decoration: underline;
        }

        /* --- Botón de Inicio de Sesión --- */
        .submit-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background-color: #e74c3c; /* Color de acento fuerte */
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        /* --- Enlaces inferiores --- */
        .footer-links {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
        }

        .footer-links p {
            margin: 10px 0;
            color: #555;
        }

        .footer-links a {
            color: #e74c3c;
            font-weight: 600;
            text-decoration: none;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Bienvenido de nuevo</h2>
        <p>Inicia sesión para gestionar tu pastelería</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group">
                <label for="email">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <span style="color: #e74c3c; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
                @error('password')
                     <span style="color: #e74c3c; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-options">
                <div class="remember-me">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Recuérdame</label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <button type="submit" class="submit-btn">
                Iniciar Sesión
            </button>
        </form>

        <!-- Botón de Google OAuth -->
        <div style="margin-top: 20px; text-align: center;">
            <p style="font-size: 13px; color: #666; margin-bottom: 10px;">O continúa con</p>
            <a href="{{ route('auth.google.redirect') }}" 
               style="display: inline-flex; align-items: center; justify-content: center; width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background-color: white; color: #333; font-size: 15px; font-weight: 500; text-decoration: none; transition: all 0.3s ease;">
                <svg width="18" height="18" viewBox="0 0 18 18" style="margin-right: 10px;">
                    <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/>
                    <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.258c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332C2.438 15.983 5.482 18 9 18z"/>
                    <path fill="#FBBC05" d="M3.964 10.707c-.18-.54-.282-1.117-.282-1.707s.102-1.167.282-1.707V4.961H.957C.347 6.175 0 7.55 0 9s.348 2.825.957 4.039l3.007-2.332z"/>
                    <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0 5.482 0 2.438 2.017.957 4.961L3.964 7.293C4.672 5.163 6.656 3.58 9 3.58z"/>
                </svg>
                Iniciar sesión con Google
            </a>
        </div>

        <div class="footer-links">
            @if (Route::has('register'))
                <p>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
            @endif
            <p>¿Necesitas ayuda? <a href="mailto:soporte@pasteleria.test">Contacta a soporte</a></p>
        </div>
    </div>
    </div>

</body>
</html>