<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal de Administración de MateFácil - Instituto de Matemáticas.">
    <title>@yield('title', 'MateFácil - Instituto de Matemáticas')</title>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @yield('styles')
</head>
<body>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <div class="dashboard-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-brand" style="flex-direction: column; padding: 1rem 0;">
                <img src="{{ asset('img/logo.jpg') }}" alt="MateFácil Logo" style="max-width: 140px; border-radius: 12px; background: white; padding: 6px;">
                <h3 style="display: none;">MATEFÁCIL</h3>
            </div>

            <ul class="sidebar-menu">
                <li class="sidebar-menu-title">Inicio</li>
                <li>
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ Route::is('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Panel Control
                    </a>
                </li>

                {{-- Gestión Académica --}}
                @if(Auth::user()->hasAnyRole(['Administrador','Director','Secretaria','Coordinador','Docente']))
                <li class="sidebar-menu-title" style="margin-top:10px;">Gestión Académica</li>
                <li>
                    <div class="sidebar-link sidebar-dropdown-toggle" onclick="toggleDropdown(this)">
                        <span style="display:flex; align-items:center; gap:12px;"><i class="bi bi-mortarboard-fill"></i> Módulo Académico</span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </div>
                    <ul class="sidebar-dropdown-menu">
                        @if(Auth::user()->hasAnyRole(['Administrador','Director','Secretaria']))
                        <li>
                            <a href="{{ route('estudiantes.index') }}" class="sidebar-link {{ Route::is('estudiantes.index') ? 'active' : '' }}">
                                <i class="bi bi-people-fill"></i> Estudiantes
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasRole('Administrador'))
                        <li>
                            <a href="{{ route('admin.docentes.index') }}" class="sidebar-link {{ Route::is('admin.docentes.*') ? 'active' : '' }}">
                                <i class="bi bi-person-video3"></i> Docentes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.empleados.index') }}" class="sidebar-link {{ Route::is('admin.empleados.*') ? 'active' : '' }}">
                                <i class="bi bi-briefcase-fill"></i> Empleados
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasAnyRole(['Administrador','Director','Coordinador','Secretaria']))
                        <li>
                            <a href="{{ route('matriculas.index') }}" class="sidebar-link {{ Route::is('matriculas.index') ? 'active' : '' }}">
                                <i class="bi bi-journal-check"></i> Matrículas
                            </a>
                        </li>
                        @endif
                        @if(Auth::user()->hasAnyRole(['Administrador','Director','Coordinador','Docente']))
                        <li>
                            <a href="{{ route('notas.index') }}" class="sidebar-link {{ Route::is('notas.index') ? 'active' : '' }}">
                                <i class="bi bi-award-fill"></i> Registro de Notas
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                {{-- Finanzas y Pagos --}}
                @if(Auth::user()->hasAnyRole(['Administrador','Director','Cajero']))
                <li class="sidebar-menu-title" style="margin-top:10px;">Finanzas y Pagos</li>
                <li>
                    <div class="sidebar-link sidebar-dropdown-toggle" onclick="toggleDropdown(this)">
                        <span style="display:flex; align-items:center; gap:12px;"><i class="bi bi-cash-stack"></i> Módulo Financiero</span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </div>
                    <ul class="sidebar-dropdown-menu">
                        <li>
                            <a href="{{ route('caja.index') }}" class="sidebar-link {{ Route::is('caja.*') ? 'active' : '' }}">
                                <i class="bi bi-safe-fill"></i> Apertura / Cierre Caja
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pagos.index') }}" class="sidebar-link {{ Route::is('pagos.index') ? 'active' : '' }}">
                                <i class="bi bi-credit-card-fill"></i> Control de Pagos
                            </a>
                        </li>
                        @if(Auth::user()->hasRole('Administrador'))
                        <li>
                            <a href="{{ route('admin.tarifas.index') }}" class="sidebar-link {{ Route::is('admin.tarifas.*') ? 'active' : '' }}">
                                <i class="bi bi-tags-fill"></i> Tarifas y Cobros
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                {{-- Configuración y Reportes --}}
                @if(Auth::user()->hasRole('Administrador'))
                <li class="sidebar-menu-title" style="margin-top:10px;">Sistema y Configuración</li>
                <li>
                    <div class="sidebar-link sidebar-dropdown-toggle" onclick="toggleDropdown(this)">
                        <span style="display:flex; align-items:center; gap:12px;"><i class="bi bi-gear-fill"></i> Ajustes Generales</span>
                        <i class="bi bi-chevron-down chevron"></i>
                    </div>
                    <ul class="sidebar-dropdown-menu">
                        <li>
                            <a href="{{ route('admin.reportes.index') }}" class="sidebar-link {{ Route::is('admin.reportes.*') ? 'active' : '' }}">
                                <i class="bi bi-bar-chart-fill"></i> Módulo de Reportes
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.especialidades.index') }}" class="sidebar-link {{ Route::is('admin.especialidades.*') ? 'active' : '' }}">
                                <i class="bi bi-bookmark-star-fill"></i> Especialidades
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.cursos.index') }}" class="sidebar-link {{ Route::is('admin.cursos.*') ? 'active' : '' }}">
                                <i class="bi bi-book-half"></i> Cursos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.grupos.index') }}" class="sidebar-link {{ Route::is('admin.grupos.*') ? 'active' : '' }}">
                                <i class="bi bi-collection-fill"></i> Grupos de Estudio
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-avatar">
                        {{ strtoupper(substr(Auth::user()->username, 0, 2)) }}
                    </div>
                    <div class="sidebar-user-info">
                        <div class="sidebar-user-name" title="{{ Auth::user()->persona ? Auth::user()->persona->nombre_completo : Auth::user()->username }}">
                            {{ Auth::user()->persona ? Auth::user()->persona->nombre_completo : Auth::user()->username }}
                        </div>
                        <div class="sidebar-user-role">
                            {{ Auth::user()->roles->first() ? Auth::user()->roles->first()->nombre : 'Usuario' }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm" style="width: 100%; border-color: rgba(239, 68, 68, 0.2); color: var(--danger);">
                        <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Workspace -->
        <main class="main-content">
            <!-- Header Banner -->
            <header class="main-header">
                <div class="page-title">
                    <h1>@yield('page_title', 'Panel de Control')</h1>
                    <p>@yield('page_description', 'Gestión general de actividades en el Instituto')</p>
                </div>
                <div class="header-actions">
                    <span class="badge badge-success" style="font-size: 0.8rem; padding: 6px 12px; display: flex; align-items: center; gap: 6px;">
                        <span style="display:inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: var(--success);"></span>
                        Base de Datos MySQL Conectada
                    </span>
                </div>
            </header>

            <!-- Dynamic Body Content -->
            @yield('content')
        </main>
    </div>

    @yield('scripts')
    <script>
        function toggleDropdown(element) {
            element.classList.toggle('open');
            let nextEl = element.nextElementSibling;
            if (nextEl && nextEl.classList.contains('sidebar-dropdown-menu')) {
                nextEl.classList.toggle('show');
            }
        }
        
        // Auto-open dropdowns if they contain the active link
        document.addEventListener("DOMContentLoaded", function() {
            let activeLinks = document.querySelectorAll('.sidebar-dropdown-menu .active');
            activeLinks.forEach(link => {
                let dropdownMenu = link.closest('.sidebar-dropdown-menu');
                if(dropdownMenu) {
                    dropdownMenu.classList.add('show');
                    let toggleBtn = dropdownMenu.previousElementSibling;
                    if(toggleBtn) {
                        toggleBtn.classList.add('open');
                    }
                }
            });
        });
    </script>
</body>
</html>
