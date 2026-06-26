<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BiblioTech')</title>
    <style>
        :root {
            --bg: #f3f4f6;
            --surface: #ffffff;
            --surface-dark: #111827;
            --surface-dark-soft: #1f2937;
            --text: #111827;
            --muted: #6b7280;
            --line: #d1d5db;
            --blue: #2563eb;
            --blue-dark: #1d4ed8;
            --green: #15803d;
            --green-soft: #dcfce7;
            --red: #b91c1c;
            --red-soft: #fee2e2;
            --warn: #c2410c;
            --warn-soft: #ffedd5;
            --shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select {
            font-family: inherit;
        }

        .admin-shell {
            display: grid;
            grid-template-columns: 260px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            background: var(--surface-dark);
            color: #f9fafb;
            display: flex;
            flex-direction: column;
            padding: 22px 16px;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .sidebar-brand {
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            margin-bottom: 18px;
            padding: 0 6px 18px;
        }

        .brand-title {
            display: block;
            font-size: 22px;
            font-weight: 700;
        }

        .brand-subtitle {
            color: #9ca3af;
            display: block;
            font-size: 13px;
            margin-top: 2px;
        }

        .sidebar-nav {
            display: grid;
            gap: 8px;
        }

        .nav-link {
            align-items: center;
            background: transparent;
            border: 0;
            border-radius: 8px;
            color: #d1d5db;
            cursor: pointer;
            display: flex;
            font-size: 14px;
            gap: 10px;
            padding: 10px 12px;
            text-align: left;
            width: 100%;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--surface-dark-soft);
            color: #ffffff;
        }

        .nav-link.active {
            border-left: 4px solid var(--blue);
            font-weight: 700;
        }

        .nav-icon {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 8px;
            display: inline-flex;
            height: 28px;
            position: relative;
            width: 28px;
        }

        .nav-icon::after {
            background: #93c5fd;
            border-radius: 999px;
            content: "";
            height: 8px;
            left: 50%;
            position: absolute;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
        }

        .nav-link.active .nav-icon {
            background: #2563eb;
            border-color: #60a5fa;
        }

        .nav-link.active .nav-icon::after {
            background: #ffffff;
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            margin-top: auto;
            padding-top: 16px;
        }

        .admin-main {
            min-width: 0;
        }

        .topbar {
            align-items: center;
            background: var(--surface);
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            gap: 18px;
            min-height: 68px;
            padding: 14px 28px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .hamburger {
            align-items: center;
            background: #f9fafb;
            border: 1px solid var(--line);
            border-radius: 8px;
            cursor: pointer;
            display: none;
            font-size: 22px;
            height: 42px;
            justify-content: center;
            width: 42px;
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 700;
        }

        .topbar-user {
            color: var(--muted);
            font-size: 14px;
        }

        .page {
            padding: 28px;
        }

        .guest-shell {
            align-items: center;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 28px;
        }

        .guest-card {
            width: min(720px, 100%);
        }

        .menu-toggle {
            display: none;
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 24px;
        }

        .page-title {
            font-size: 28px;
            margin: 0 0 8px;
        }

        .page-description {
            color: var(--muted);
            margin: 0;
            max-width: 760px;
        }

        .grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-top: 22px;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            min-height: 170px;
            padding: 20px;
            position: relative;
        }

        .card::before {
            background: var(--blue);
            border-radius: 8px 8px 0 0;
            content: "";
            height: 4px;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
        }

        .card-accent-green::before { background: var(--green); }
        .card-accent-warn::before { background: #f97316; }
        .card-accent-red::before { background: var(--red); }

        .card h2 {
            font-size: 18px;
            margin: 0 0 8px;
        }

        .card p {
            color: var(--muted);
            margin: 0 0 18px;
        }

        .card-footer {
            margin-top: auto;
        }

        .button,
        .button-small,
        .button-danger,
        .button-secondary {
            align-items: center;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            font: inherit;
            font-weight: 700;
            justify-content: center;
            text-align: center;
        }

        .button {
            background: var(--blue);
            border: 0;
            color: #ffffff;
            min-width: 132px;
            padding: 10px 14px;
        }

        .button:hover { background: var(--blue-dark); }

        .button-secondary {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: var(--blue);
            padding: 9px 13px;
        }

        .button-secondary:hover { background: #dbeafe; }

        .button-small {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: var(--blue);
            font-size: 13px;
            padding: 7px 10px;
        }

        .button-danger {
            background: var(--red-soft);
            border: 1px solid #fecaca;
            color: var(--red);
            font-size: 13px;
            padding: 7px 10px;
        }

        .action-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .status {
            align-items: center;
            background: var(--green-soft);
            border: 1px solid #86efac;
            border-radius: 8px;
            color: var(--green);
            display: flex;
            font-weight: 700;
            gap: 8px;
            margin-top: 20px;
            padding: 14px 16px;
        }

        .content-split {
            display: grid;
            gap: 18px;
            grid-template-columns: minmax(0, 1.5fr) minmax(300px, 0.9fr);
            margin-top: 18px;
        }

        .form-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 20px;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-field label {
            font-weight: 700;
        }

        .form-field input,
        .form-field select {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text);
            font: inherit;
            min-height: 42px;
            padding: 9px 11px;
            width: 100%;
        }

        .form-field input:focus,
        .form-field select:focus {
            border-color: var(--blue);
            outline: 3px solid #dbeafe;
        }

        .form-help,
        .error-text {
            color: var(--muted);
            font-size: 13px;
        }

        .error-text {
            color: var(--red);
            font-weight: 700;
        }

        .form-actions {
            margin-top: 18px;
        }

        .rules-list {
            color: var(--muted);
            margin: 16px 0 0;
            padding-left: 20px;
        }

        .result-card {
            border-radius: 8px;
            margin-top: 18px;
            padding: 16px;
        }

        .result-card h2 {
            font-size: 18px;
            margin: 0 0 8px;
        }

        .result-card p {
            margin: 0;
        }

        .result-valid {
            background: var(--green-soft);
            border: 1px solid #86efac;
            color: var(--green);
        }

        .result-invalid {
            background: var(--red-soft);
            border: 1px solid #fecaca;
            color: var(--red);
        }

        .result-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .result-warning {
            background: var(--warn-soft);
            border: 1px solid #fed7aa;
            color: var(--warn);
        }

        .case-grid {
            display: grid;
            gap: 12px;
            margin-top: 16px;
        }

        .case-card {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 14px;
        }

        .case-card h3 {
            font-size: 15px;
            margin: 0 0 8px;
        }

        .case-card dl {
            display: grid;
            gap: 4px;
            grid-template-columns: auto 1fr;
            margin: 0;
        }

        .case-card dt {
            color: var(--muted);
            font-weight: 700;
        }

        .case-card dd {
            margin: 0;
        }

        .code-pill {
            background: #f9fafb;
            border: 1px solid var(--line);
            border-radius: 6px;
            display: inline-block;
            font-family: Consolas, monospace;
            padding: 1px 6px;
        }

        .table-wrap {
            border: 1px solid var(--line);
            border-radius: 8px;
            margin-top: 18px;
            overflow-x: auto;
        }

        .table {
            border-collapse: collapse;
            min-width: 760px;
            width: 100%;
        }

        .table th,
        .table td {
            border-bottom: 1px solid var(--line);
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        .table th {
            background: #f9fafb;
            font-size: 13px;
            text-transform: uppercase;
        }

        .table tr:last-child td {
            border-bottom: 0;
        }

        .badge {
            border-radius: 999px;
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 8px;
        }

        .badge-green {
            background: var(--green-soft);
            color: var(--green);
        }

        .badge-warn {
            background: var(--warn-soft);
            color: var(--warn);
        }

        .badge-red {
            background: var(--red-soft);
            color: var(--red);
        }

        .badge-blue {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .notice {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            color: #1e40af;
            margin-top: 18px;
            padding: 14px 16px;
        }

        .modal {
            align-items: center;
            background: rgba(15, 23, 42, 0.52);
            bottom: 0;
            display: none;
            justify-content: center;
            left: 0;
            padding: 24px;
            position: fixed;
            right: 0;
            top: 0;
            z-index: 50;
        }

        .modal:target {
            display: flex;
        }

        .modal-dialog {
            background: var(--surface);
            border-radius: 8px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
            max-height: calc(100vh - 48px);
            overflow-y: auto;
            padding: 24px;
            width: min(720px, 100%);
        }

        .modal-header {
            align-items: flex-start;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .modal-close {
            align-items: center;
            background: #f9fafb;
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--muted);
            display: inline-flex;
            font-weight: 700;
            height: 36px;
            justify-content: center;
            width: 36px;
        }

        @media (max-width: 980px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .hamburger {
                display: inline-flex;
            }

            .sidebar {
                bottom: 0;
                height: auto;
                left: 0;
                max-width: 290px;
                position: fixed;
                top: 0;
                transform: translateX(-100%);
                transition: transform 0.2s ease;
                width: 82vw;
                z-index: 30;
            }

            .menu-toggle:checked ~ .admin-shell .sidebar {
                transform: translateX(0);
            }

            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-split {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .page,
            .guest-shell {
                padding: 18px;
            }

            .grid,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 24px;
            }

            .topbar {
                padding: 12px 18px;
            }
        }
    </style>
</head>
<body>
    @php
        $segmentoPortal = request()->segment(1);
        $esPortal = in_array($segmentoPortal, ['alumno', 'docente', 'admin'], true);
        $esContextoPublico = request()->routeIs('public.*') || request()->routeIs('registro.*') || request()->routeIs('portal.login*');
        $usuarioPortal = session('portal_user', []);
        $nombrePortal = Auth::user()?->name
            ?? ($usuarioPortal['name'] ?? ($segmentoPortal === 'admin' ? 'Administrador' : 'Visitante'));

        $portalHome = match ($segmentoPortal) {
            'alumno' => route('alumno.dashboard'),
            'docente' => route('docente.dashboard'),
            'admin' => route('admin.dashboard'),
            default => route('public.home'),
        };

        $portalSubtitulo = match ($segmentoPortal) {
            'alumno' => 'Portal del alumno',
            'docente' => 'Portal docente',
            'admin' => 'Administracion',
            default => 'Biblioteca Principal',
        };
    @endphp

    @if (!$esContextoPublico && (Auth::check() || $esPortal))
        <input class="menu-toggle" id="menu-toggle" type="checkbox">

        <div class="admin-shell">
            <aside class="sidebar">
                <a class="sidebar-brand" href="{{ $portalHome }}">
                    <span class="brand-title">BiblioTech</span>
                    <span class="brand-subtitle">{{ $portalSubtitulo }}</span>
                </a>

                <nav class="sidebar-nav" aria-label="Menu principal">
                    @if ($segmentoPortal === 'alumno')
                        <a @class(['nav-link', 'active' => request()->routeIs('alumno.dashboard')]) href="{{ route('alumno.dashboard') }}">
                            <span class="nav-icon"></span> Dashboard
                        </a>
                        <a @class(['nav-link', 'active' => request()->routeIs('alumno.catalogo')]) href="{{ route('alumno.catalogo') }}">
                            <span class="nav-icon"></span> Catalogo
                        </a>
                        <a @class(['nav-link', 'active' => request()->routeIs('alumno.estado-cuenta')]) href="{{ route('alumno.estado-cuenta') }}">
                            <span class="nav-icon"></span> Estado de cuenta
                        </a>
                    @elseif ($segmentoPortal === 'docente')
                        <a @class(['nav-link', 'active' => request()->routeIs('docente.dashboard')]) href="{{ route('docente.dashboard') }}">
                            <span class="nav-icon"></span> Dashboard
                        </a>
                        <a @class(['nav-link', 'active' => request()->routeIs('docente.catalogo')]) href="{{ route('docente.catalogo') }}">
                            <span class="nav-icon"></span> Catalogo
                        </a>
                        <a @class(['nav-link', 'active' => request()->routeIs('docente.estado-cuenta')]) href="{{ route('docente.estado-cuenta') }}">
                            <span class="nav-icon"></span> Estado de cuenta
                        </a>
                        <a @class(['nav-link', 'active' => request()->routeIs('docente.pagar-deuda')]) href="{{ route('docente.pagar-deuda') }}">
                            <span class="nav-icon"></span> Pagar deuda
                        </a>
                    @else
                        <a @class(['nav-link', 'active' => request()->routeIs('admin.dashboard') || request()->routeIs('dashboard')]) href="{{ route('admin.dashboard') }}">
                            <span class="nav-icon"></span> Dashboard
                        </a>
                        <a @class(['nav-link', 'active' => request()->routeIs('admin.usuarios.*')]) href="{{ route('admin.usuarios.index') }}">
                            <span class="nav-icon"></span> Usuarios
                        </a>
                        <a @class(['nav-link', 'active' => request()->routeIs('libros.*')]) href="{{ route('libros.index') }}">
                            <span class="nav-icon"></span> Libros
                        </a>
                        <a @class(['nav-link', 'active' => request()->routeIs('prestamos.*')]) href="{{ route('prestamos.index') }}">
                            <span class="nav-icon"></span> Prestamos
                        </a>
                        <a @class(['nav-link', 'active' => request()->routeIs('morosidad.*')]) href="{{ route('morosidad.index') }}">
                            <span class="nav-icon"></span> Morosidad
                        </a>
                        <a @class(['nav-link', 'active' => request()->routeIs('pagos.*')]) href="{{ route('pagos.index') }}">
                            <span class="nav-icon"></span> Pagos
                        </a>
                    @endif
                </nav>

                <div class="sidebar-footer">
                    @if (Auth::check())
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="nav-link" type="submit">
                                <span class="nav-icon"></span> Cerrar sesion
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('portal.logout') }}">
                            @csrf
                            <button class="nav-link" type="submit">
                                <span class="nav-icon"></span> Salir
                            </button>
                        </form>
                    @endif
                </div>
            </aside>

            <div class="admin-main">
                <header class="topbar">
                    <label class="hamburger" for="menu-toggle" aria-label="Abrir menu">&#9776;</label>
                    <div>
                        <div class="topbar-title">@yield('section-title', 'Panel de administracion')</div>
                        <div class="topbar-user">{{ $nombrePortal }}</div>
                    </div>
                </header>

                <main class="page">
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <main class="guest-shell">
            <div class="guest-card">
                <nav class="action-row" style="margin-bottom: 18px;">
                    <a class="button-secondary" href="{{ route('portal.login') }}">Login</a>
                    <a class="button-secondary" href="{{ route('registro.index') }}">Registrarse</a>
                    <a class="button" href="{{ route('login') }}">Acceso Admin</a>
                </nav>
                @yield('content')
            </div>
        </main>
    @endif
</body>
</html>
