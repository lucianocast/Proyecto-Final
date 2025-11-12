<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenido a tu Sistema de Pastelería</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- Estilos Generales y Fondo Animado --- */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            color: #333;
            background: linear-gradient(135deg, #fde7e7, #fef8e8, #e6f9f5);
            background-size: 400% 400%;
            animation: gradientAnimation 18s ease infinite;
            height: 100vh;
            overflow: hidden; /* Evita barras de scroll por la animación */
            position: relative; /* Necesario para posicionar los pasteles */
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- Navegación Superior (Login/Registro) --- */
        .top-nav {
            position: absolute;
            top: 25px;
            right: 40px;
            z-index: 10;
        }

        .top-nav a {
            color: #5a5a5a;
            text-decoration: none;
            font-weight: 600;
            margin-left: 28px;
            font-size: 16px;
            transition: color 0.3s ease, transform 0.3s ease;
            display: inline-block;
        }

        .top-nav a:hover {
            color: #000;
            transform: translateY(-2px);
        }

        /* --- Contenedor Principal del Mensaje --- */
        .welcome-container {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
            padding: 20px;
            z-index: 5; /* Asegura que el contenido esté por encima de los pasteles */
            position: relative;
        }

        /* --- Contenido de Bienvenida con efecto "Glass" --- */
        .welcome-content {
            background: rgba(255, 255, 255, 0.25);
            padding: 50px 70px;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            animation: contentFadeInUp 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
            opacity: 0;
        }

        @keyframes contentFadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-content h1 {
            font-size: 3.8em;
            margin-bottom: 10px;
            font-weight: 700;
            color: #2c3e50;
        }

        .welcome-content p {
            font-size: 1.25em;
            color: #34495e;
            max-width: 600px;
            font-weight: 300;
            line-height: 1.6;
        }

        /* --- Estilos y Animaciones de los Pasteles Flotantes --- */
        .cake-animation-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; /* Permite hacer clic en el contenido debajo */
            z-index: 1; /* Para que estén detrás del contenido principal */
            overflow: hidden; /* Asegura que los pasteles no se salgan del body */
        }

        .cake-item {
            position: absolute;
            width: 80px; /* Tamaño de los pasteles, ajusta si es necesario */
            height: 80px;
            background-size: cover;
            background-position: center;
            opacity: 0.7;
            animation: floatAndFade 15s infinite ease-in-out;
            border-radius: 50%; /* Para darle forma más suave a las imágenes */
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* Definimos los tipos de pasteles. Puedes añadir más si tienes imágenes. */
        .cake-item.cake1 {
            background-image: url('{{ asset('images/cake1.png') }}'); /* Pastel con fresas */
        }
        .cake-item.cake2 {
            background-image: url('{{ asset('images/cake2.png') }}'); /* Cupcake */
        }
        .cake-item.cake3 {
            background-image: url('{{ asset('images/cake3.png') }}'); /* Trozo de pastel */
        }
        .cake-item.cake4 {
            background-image: url('{{ asset('images/cake4.png') }}'); /* Pastel de chocolate */
        }
        .cake-item.cake5 {
            background-image: url('{{ asset('images/cake5.png') }}'); /* Galleta o macaron */
        }

        /* Animación principal de flotación y desvanecimiento */
        @keyframes floatAndFade {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.7;
                filter: blur(0px);
            }
            50% {
                transform: translateY(-20px) rotate(5deg); /* Ligera oscilación */
                opacity: 0.8;
                filter: blur(0px);
            }
            100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0.7;
                filter: blur(0px);
            }
        }

        /* Animación para que los pasteles aparezcan y se muevan por la pantalla */
        /* Cada pastel tendrá su propia posición inicial y animación para variar */
        .cake-item:nth-child(1) {
            top: 10%; left: 5%;
            animation-duration: 20s;
            animation-delay: 0s;
            transform: scale(0.8);
            opacity: 0.6;
            animation: floatAndMove1 20s infinite ease-in-out;
        }
        .cake-item:nth-child(2) {
            top: 25%; left: 80%;
            animation-duration: 22s;
            animation-delay: 2s;
            transform: scale(1);
            opacity: 0.7;
            animation: floatAndMove2 22s infinite ease-in-out;
        }
        .cake-item:nth-child(3) {
            top: 60%; left: 15%;
            animation-duration: 18s;
            animation-delay: 4s;
            transform: scale(0.9);
            opacity: 0.65;
            animation: floatAndMove3 18s infinite ease-in-out;
        }
        .cake-item:nth-child(4) {
            top: 70%; left: 70%;
            animation-duration: 25s;
            animation-delay: 6s;
            transform: scale(1.1);
            opacity: 0.8;
            animation: floatAndMove4 25s infinite ease-in-out;
        }
        .cake-item:nth-child(5) {
            top: 40%; left: 30%;
            animation-duration: 19s;
            animation-delay: 8s;
            transform: scale(0.7);
            opacity: 0.5;
            animation: floatAndMove5 19s infinite ease-in-out;
        }
        .cake-item:nth-child(6) {
            top: 5%; left: 50%;
            animation-duration: 21s;
            animation-delay: 1s;
            transform: scale(0.95);
            opacity: 0.75;
            animation: floatAndMove6 21s infinite ease-in-out;
        }
        .cake-item:nth-child(7) {
            top: 85%; left: 40%;
            animation-duration: 23s;
            animation-delay: 3s;
            transform: scale(0.85);
            opacity: 0.6;
            animation: floatAndMove7 23s infinite ease-in-out;
        }
        .cake-item:nth-child(8) {
            top: 20%; left: 10%;
            animation-duration: 20s;
            animation-delay: 5s;
            transform: scale(1.05);
            opacity: 0.7;
            animation: floatAndMove8 20s infinite ease-in-out;
        }

        /* Definiciones de animaciones individuales para cada pastel */
        @keyframes floatAndMove1 {
            0%, 100% { transform: translate(0, 0) scale(0.8); opacity: 0.6; }
            25% { transform: translate(50px, -30px) scale(0.85); opacity: 0.7; }
            50% { transform: translate(0, -60px) scale(0.8); opacity: 0.6; }
            75% { transform: translate(-50px, -30px) scale(0.75); opacity: 0.55; }
        }
        @keyframes floatAndMove2 {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.7; }
            25% { transform: translate(-60px, 40px) scale(0.95); opacity: 0.65; }
            50% { transform: translate(0, 80px) scale(1); opacity: 0.7; }
            75% { transform: translate(60px, 40px) scale(1.05); opacity: 0.75; }
        }
        @keyframes floatAndMove3 {
            0%, 100% { transform: translate(0, 0) scale(0.9); opacity: 0.65; }
            25% { transform: translate(40px, -20px) scale(0.88); opacity: 0.6; }
            50% { transform: translate(0, -40px) scale(0.9); opacity: 0.65; }
            75% { transform: translate(-40px, -20px) scale(0.92); opacity: 0.7; }
        }
        @keyframes floatAndMove4 {
            0%, 100% { transform: translate(0, 0) scale(1.1); opacity: 0.8; }
            25% { transform: translate(-70px, -30px) scale(1.08); opacity: 0.75; }
            50% { transform: translate(0, -60px) scale(1.1); opacity: 0.8; }
            75% { transform: translate(70px, -30px) scale(1.12); opacity: 0.85; }
        }
        @keyframes floatAndMove5 {
            0%, 100% { transform: translate(0, 0) scale(0.7); opacity: 0.5; }
            25% { transform: translate(30px, 50px) scale(0.72); opacity: 0.55; }
            50% { transform: translate(0, 100px) scale(0.7); opacity: 0.5; }
            75% { transform: translate(-30px, 50px) scale(0.68); opacity: 0.45; }
        }
        @keyframes floatAndMove6 {
            0%, 100% { transform: translate(0, 0) scale(0.95); opacity: 0.75; }
            25% { transform: translate(20px, -40px) scale(0.93); opacity: 0.7; }
            50% { transform: translate(0, -80px) scale(0.95); opacity: 0.75; }
            75% { transform: translate(-20px, -40px) scale(0.97); opacity: 0.8; }
        }
        @keyframes floatAndMove7 {
            0%, 100% { transform: translate(0, 0) scale(0.85); opacity: 0.6; }
            25% { transform: translate(-50px, 20px) scale(0.83); opacity: 0.55; }
            50% { transform: translate(0, 40px) scale(0.85); opacity: 0.6; }
            75% { transform: translate(50px, 20px) scale(0.87); opacity: 0.65; }
        }
        @keyframes floatAndMove8 {
            0%, 100% { transform: translate(0, 0) scale(1.05); opacity: 0.7; }
            25% { transform: translate(60px, -25px) scale(1.03); opacity: 0.65; }
            50% { transform: translate(0, -50px) scale(1.05); opacity: 0.7; }
            75% { transform: translate(-60px, -25px) scale(1.07); opacity: 0.75; }
        }
    </style>
</head>
<body class="antialiased">

    {{-- Navegación Superior (Login/Registro) --}}
    <div class="top-nav">
        @auth
            <a href="{{ url('/encargado') }}">Panel de Encargado</a>
        @else
            <a href="{{ route('login') }}">Iniciar Sesión</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}">Registrarse</a>
            @endif
        @endauth
    </div>

    {{-- Contenedor del Mensaje de Bienvenida --}}
    <div class="welcome-container">
        <div class="welcome-content">
            <h1>SG Pastelerías</h1>
            <p>La plataforma para gestionar tu pastelería. Controla compras, proveedores, producción y stock de forma sencilla y eficiente.</p>
        </div>
    </div>

    {{-- Contenedor de las Animaciones de Pasteles --}}
    <div class="cake-animation-wrapper">
        {{-- Cada div.cake-item representa un pastel --}}
        <div class="cake-item cake1"></div>
        <div class="cake-item cake2"></div>
        <div class="cake-item cake3"></div>
        <div class="cake-item cake4"></div>
        <div class="cake-item cake5"></div>
        <div class="cake-item cake1"></div> {{-- Se repiten para más variedad --}}
        <div class="cake-item cake2"></div>
        <div class="cake-item cake3"></div>
    </div>

</body>
</html>