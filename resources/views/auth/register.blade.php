<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Pastelería</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- Estilos Generales y Fondo Animado (Idéntico al login) --- */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            color: #333;
            background: linear-gradient(135deg, #fde7e7, #fef8e8, #e6f9f5);
            background-size: 400% 400%;
            animation: gradientAnimation 18s ease infinite;
            min-height: 100vh; /* Usar min-height para asegurar que cubre toda la pantalla */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px 0; /* Añadir padding vertical para pantallas pequeñas */
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- Tarjeta de Registro con Efecto "Glass" --- */
        .register-card {
            background: rgba(255, 255, 255, 0.3);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            width: 100%;
            max-width: 450px; /* Un poco más ancha para los campos extra */
            box-sizing: border-box;
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
        .register-card h2 {
            text-align: center;
            font-weight: 600;
            font-size: 28px;
            margin-top: 0;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .register-card p {
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
            border-color: #f79a9a;
            box-shadow: 0 0 0 3px rgba(253, 231, 231, 0.6);
        }
        
        /* Mensaje de error */
        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        /* --- Botón de Registro --- */
        .submit-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background-color: #e74c3c;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 10px;
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
        
        .terms-text {
            font-size: 12px;
            color: #777;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <h2>Crea tu cuenta</h2>
        <p>Regístrate para comenzar a administrar tu pastelería</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="input-group">
                <label for="name">Nombre completo</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group">
                <label for="email">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
                 @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="input-group">
                <label for="password-confirm">Confirmar contraseña</label>
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="submit-btn">
                Registrarse
            </button>

            <div class="footer-links">
                <p>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
            </div>
            
             <p class="terms-text">
                Al registrarte, aceptas nuestros Términos y Condiciones.
            </p>
        </form>
    </div>

</body>
</html>