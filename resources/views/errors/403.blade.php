<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado | Instituto de Matemáticas UPDS</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            padding: 2rem;
            gap: 1.5rem;
            position: relative;
            z-index: 10;
        }
        .error-icon {
            font-size: 5rem;
            color: var(--danger);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }
        .error-code {
            font-size: 6rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--danger), #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
        }
        .error-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(24px);
            border-radius: 1.5rem;
            padding: 3rem;
            max-width: 500px;
            width: 100%;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
        }
        .error-desc {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .role-badge-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .role-badge {
            background: rgba(99,102,241,0.15);
            border: 1px solid rgba(99,102,241,0.3);
            color: #a5b4fc;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 999px;
            font-weight: 600;
        }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--accent-blue), var(--accent-cyan));
            color: white;
            padding: 12px 28px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99,102,241,0.4);
        }
    </style>
</head>
<body>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <div class="error-container">
        <div class="error-card">
            <div class="error-icon"><i class="bi bi-shield-lock-fill"></i></div>
            <div class="error-code">403</div>
            <div class="error-title">Acceso Denegado</div>
            <div class="error-desc">
                No tiene los permisos necesarios para acceder a este módulo del sistema.
                Contacte al Administrador si cree que esto es un error.
            </div>

            <div style="margin-bottom: 1rem; color: var(--text-muted); font-size: 0.85rem;">
                Su rol actual:
            </div>
            <div class="role-badge-group">
                @auth
                    @foreach(Auth::user()->roles as $rol)
                        <span class="role-badge"><i class="bi bi-person-badge"></i> {{ $rol->nombre }}</span>
                    @endforeach
                @endauth
            </div>

            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('dashboard') }}" class="btn-back">
                    <i class="bi bi-grid-1x2-fill"></i> Ir al Panel Principal
                </a>
                @auth
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: var(--danger); padding: 12px 24px; border-radius: 10px; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 1rem;">
                        <i class="bi bi-box-arrow-left"></i> Cambiar sesión
                    </button>
                </form>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
