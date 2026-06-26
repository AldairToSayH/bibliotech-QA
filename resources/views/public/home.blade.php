@extends('layouts.app')

@section('title', 'BiblioTech - Inicio')

@section('content')
    <style>
        .public-landing {
            display: grid;
            gap: 22px;
        }

        .public-hero {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: var(--shadow);
            display: grid;
            gap: 24px;
            grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
            padding: 32px;
        }

        .public-brand {
            color: var(--blue);
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .public-title {
            font-size: 36px;
            letter-spacing: 0;
            line-height: 1.08;
            margin: 0 0 14px;
        }

        .public-actions {
            display: grid;
            gap: 12px;
        }

        .public-action {
            align-items: center;
            background: #f9fafb;
            border: 1px solid var(--line);
            border-radius: 8px;
            display: flex;
            gap: 14px;
            justify-content: space-between;
            padding: 16px;
        }

        .public-action strong {
            display: block;
            margin-bottom: 2px;
        }

        .public-action span {
            color: var(--muted);
            display: block;
            font-size: 14px;
        }

        .public-action.is-primary {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .public-summary {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        @media (max-width: 820px) {
            .public-hero,
            .public-summary {
                grid-template-columns: 1fr;
            }

            .public-title {
                font-size: 28px;
            }
        }
    </style>

    <section class="public-landing">
        <div class="public-hero">
            <div>
                <div class="public-brand">BiblioTech</div>
                <h1 class="public-title">Biblioteca Principal</h1>
                <p class="page-description">
                    Accede a tus servicios de biblioteca, registra una cuenta institucional o entra al panel administrativo.
                </p>
            </div>

            <div class="public-actions" aria-label="Accesos principales">
                <a class="public-action is-primary" href="{{ route('portal.login') }}">
                    <div>
                        <strong>Login</strong>
                        <span>Alumnos y docentes registrados.</span>
                    </div>
                    <span class="badge badge-blue">Entrar</span>
                </a>

                <a class="public-action" href="{{ route('registro.index') }}">
                    <div>
                        <strong>Registrarse</strong>
                        <span>Alta institucional para alumno o docente.</span>
                    </div>
                    <span class="badge badge-green">Nuevo</span>
                </a>

                <a class="public-action" href="{{ route('login') }}">
                    <div>
                        <strong>Acceso Admin</strong>
                        <span>Panel interno de administracion.</span>
                    </div>
                    <span class="badge badge-warn">Admin</span>
                </a>
            </div>
        </div>

        <div class="public-summary">
            <article class="panel">
                <h2 style="margin-top: 0;">Alumnos</h2>
                <p class="page-description">Consulta catalogo y estado de cuenta desde tu portal.</p>
            </article>

            <article class="panel">
                <h2 style="margin-top: 0;">Docentes</h2>
                <p class="page-description">Revisa prestamos, pagos y deudas institucionales.</p>
            </article>

            <article class="panel">
                <h2 style="margin-top: 0;">Administracion</h2>
                <p class="page-description">Gestiona usuarios, libros, prestamos, morosidad y pagos.</p>
            </article>
        </div>
    </section>
@endsection
