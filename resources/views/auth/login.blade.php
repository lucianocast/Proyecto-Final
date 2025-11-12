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

            <div class="footer-links">
                @if (Route::has('register'))
                    <p>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a></p>
                @endif
                <p>¿Necesitas ayuda? <a href="mailto:soporte@pasteleria.test">Contacta a soporte</a></p>
            </div>
        </form>
    </div>

</body>
</html>