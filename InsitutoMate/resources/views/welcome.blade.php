<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MateFácil - Instituto de Matemáticas</title>
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            text-align: center;
            margin: 0;
            padding: 2rem;
            position: relative;
        }
        
        .welcome-container {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius-lg);
            padding: 3rem 4rem;
            max-width: 600px;
            box-shadow: var(--shadow-lg);
            position: relative;
            z-index: 10;
        }
        
        .logo {
            max-width: 280px;
            margin-bottom: 2rem;
            border-radius: 16px;
            background: white;
            padding: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        h1 {
            font-family: var(--font-heading);
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--accent-blue);
        }
        
        p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent-blue) 0%, var(--accent-green) 100%);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: var(--transition-smooth);
            border: none;
            cursor: pointer;
            box-shadow: var(--shadow-md);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px var(--accent-blue);
        }
        
        /* Decoraciones de fondo */
        .bg-blob-1, .bg-blob-2 {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            z-index: 1;
            opacity: 0.5;
        }
        
        .bg-blob-1 {
            background: var(--accent-blue);
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
        }
        
        .bg-blob-2 {
            background: var(--accent-green);
            width: 300px;
            height: 300px;
            bottom: -50px;
            right: -50px;
        }
    </style>
</head>
<body>
    <div class="bg-blob-1"></div>
    <div class="bg-blob-2"></div>
    
    <div class="welcome-container">
        <img src="{{ asset('img/logo.jpg') }}" alt="MateFácil Logo" class="logo">
        <h1>Bienvenidos al Sistema</h1>
        <p>Plataforma de gestión académica y financiera del Instituto de Matemáticas MateFácil. Por favor, inicia sesión para acceder a tu panel de control.</p>
        
        @auth
            <a href="{{ route('dashboard') }}" class="btn">Ir al Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn">Iniciar Sesión</a>
        @endauth
    </div>
</body>
</html>
