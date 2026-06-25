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
            --text: #111827;
            --muted: #6b7280;
            --line: #d1d5db;
            --blue: #2563eb;
            --blue-dark: #1d4ed8;
            --green: #15803d;
            --green-soft: #dcfce7;
            --red: #b91c1c;
            --red-soft: #fee2e2;
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

        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--line);
        }

        .topbar-inner {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 16px 0;
        }

        .brand {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .brand-title {
            font-size: 22px;
            font-weight: 700;
        }

        .brand-subtitle {
            color: var(--muted);
            font-size: 13px;
        }

        .nav {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .nav-link {
            border: 1px solid var(--line);
            border-radius: 8px;
            color: #374151;
            font-size: 14px;
            padding: 8px 10px;
        }

        .nav-link:hover {
            border-color: var(--blue);
            color: var(--blue);
        }

        .page {
            width: min(1120px, calc(100% - 32px));
            margin: 0 auto;
            padding: 28px 0 48px;
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 8px;
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
            display: flex;
            flex-direction: column;
            min-height: 190px;
            padding: 20px;
        }

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

        .button {
            background: var(--blue);
            border: 0;
            border-radius: 8px;
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            font: inherit;
            font-weight: 700;
            justify-content: center;
            min-width: 132px;
            padding: 10px 14px;
        }

        .button:hover {
            background: var(--blue-dark);
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

        .placeholder {
            margin-top: 22px;
        }

        .placeholder strong {
            color: var(--blue);
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
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #ffffff;
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
            background: #ffedd5;
            border: 1px solid #fed7aa;
            color: #c2410c;
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
            min-width: 680px;
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
            background: #ffedd5;
            color: #c2410c;
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

        @media (max-width: 860px) {
            .topbar-inner {
                align-items: flex-start;
                flex-direction: column;
            }

            .nav {
                justify-content: flex-start;
            }

            .grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-split {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="{{ route('dashboard') }}">
                <span class="brand-title">BiblioTech</span>
                <span class="brand-subtitle">Biblioteca academica con pruebas PHPUnit</span>
            </a>

            <nav class="nav" aria-label="Navegacion principal">
                <a class="nav-link" href="{{ route('dashboard') }}">Inicio</a>
                <a class="nav-link" href="{{ route('registro.index') }}">Registro</a>
                <a class="nav-link" href="{{ route('libros.index') }}">Libros</a>
                <a class="nav-link" href="{{ route('prestamos.index') }}">Prestamos</a>
                <a class="nav-link" href="{{ route('morosidad.index') }}">Morosidad</a>
                <a class="nav-link" href="{{ route('pagos.index') }}">Pagos</a>
                <a class="nav-link" href="{{ route('pruebas.index') }}">Pruebas</a>
            </nav>
        </div>
    </header>

    <main class="page">
        @yield('content')
    </main>
</body>
</html>
