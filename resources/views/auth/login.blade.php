<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal de Acceso del Instituto de Matemáticas UPDS. Inicia sesión para gestionar personas, matrículas, notas y pagos.">
    <title>Iniciar Sesión | Instituto de Matemáticas UPDS</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-logo">
                <i class="bi bi-infinity"></i>
                <h2>INSTITUTO MATEMÁTICO</h2>
                <p>Sistema de Gestión Académica UPDS</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <div>
                        @foreach ($errors->all() as $error)
                            <p><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="username" class="form-label">Usuario</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-secondary);">
                            <i class="bi bi-person-fill"></i>
                        </span>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Ingrese su usuario" style="padding-left: 40px;" value="{{ old('username') }}" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 25px;">
                    <label for="password" class="form-label">Contraseña</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-secondary);">
                            <i class="bi bi-lock-fill"></i>
                        </span>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Ingrese su contraseña" style="padding-left: 40px;" required autocomplete="current-password">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 14px;">
                    Entrar <i class="bi bi-arrow-right-short"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
