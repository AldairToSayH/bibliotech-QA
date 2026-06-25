<?php

namespace App\Services;

use App\Models\Libro;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use DateInterval;
use DateTimeImmutable;

class PrestamoService
{
    private const DIAS_PRESTAMO_POR_ROL = [
        'estudiante' => 7,
        'docente' => 14,
    ];

    /**
     * Calcula el plazo de prestamo segun el rol del usuario.
     *
     * @return array{
     *     valido: bool,
     *     rol: string|null,
     *     dias_prestamo: int,
     *     fecha_prestamo: string,
     *     fecha_devolucion: string|null,
     *     mensaje: string
     * }
     */
    public function calcularPlazoPrestamo(string $rol, string $fechaPrestamo): array
    {
        if (!array_key_exists($rol, self::DIAS_PRESTAMO_POR_ROL)) {
            return [
                'valido' => false,
                'rol' => null,
                'dias_prestamo' => 0,
                'fecha_prestamo' => $fechaPrestamo,
                'fecha_devolucion' => null,
                'mensaje' => 'Rol no valido para prestamo.',
            ];
        }

        $diasPrestamo = self::DIAS_PRESTAMO_POR_ROL[$rol];
        $fecha = new DateTimeImmutable($fechaPrestamo);
        $fechaDevolucion = $fecha->add(new DateInterval('P' . $diasPrestamo . 'D'));

        return [
            'valido' => true,
            'rol' => $rol,
            'dias_prestamo' => $diasPrestamo,
            'fecha_prestamo' => $fecha->format('Y-m-d'),
            'fecha_devolucion' => $fechaDevolucion->format('Y-m-d'),
            'mensaje' => 'Plazo de prestamo calculado correctamente.',
        ];
    }

    /**
     * Registra un prestamo real cuando el libro esta disponible.
     *
     * @return array{
     *     valido: bool,
     *     prestamo_id: int|null,
     *     libro_estado: string|null,
     *     fecha_devolucion: string|null,
     *     mensaje: string
     * }
     */
    public function registrarPrestamo(int $userId, int $libroId, string $rol, string $fechaPrestamo): array
    {
        $usuario = User::find($userId);

        if (!$usuario) {
            return $this->respuestaPrestamoInvalido('Usuario no encontrado.');
        }

        $libro = Libro::find($libroId);

        if (!$libro) {
            return $this->respuestaPrestamoInvalido('Libro no encontrado.');
        }

        if ($libro->estado !== 'DISPONIBLE') {
            return $this->respuestaPrestamoInvalido('El libro no esta disponible.');
        }

        $plazo = $this->calcularPlazoPrestamo($rol, $fechaPrestamo);

        if (!$plazo['valido']) {
            return $this->respuestaPrestamoInvalido($plazo['mensaje']);
        }

        $prestamo = Prestamo::create([
            'user_id' => $usuario->id,
            'libro_id' => $libro->id,
            'fecha_prestamo' => $plazo['fecha_prestamo'],
            'fecha_devolucion' => $plazo['fecha_devolucion'],
            'estado' => 'ACTIVO',
        ]);

        $libro->update(['estado' => 'PRESTADO']);

        return [
            'valido' => true,
            'prestamo_id' => $prestamo->id,
            'libro_estado' => 'PRESTADO',
            'fecha_devolucion' => $plazo['fecha_devolucion'],
            'mensaje' => 'Prestamo registrado correctamente.',
        ];
    }

    /**
     * Registra un prestamo solo si el usuario no tiene penalizacion activa.
     *
     * @return array{
     *     valido: bool,
     *     prestamo_id: int|null,
     *     libro_estado: string|null,
     *     fecha_devolucion: string|null,
     *     puede_prestar: bool,
     *     mensaje: string
     * }
     */
    public function registrarPrestamoValidandoPenalizacion(
        int $userId,
        int $libroId,
        string $rol,
        string $fechaPrestamo
    ): array {
        $pagoPenalizado = Pago::where('user_id', $userId)
            ->where('estado', 'PENALIZADO')
            ->whereNotNull('fecha_habilitacion')
            ->orderByDesc('fecha_habilitacion')
            ->first();

        if ($pagoPenalizado) {
            $fechaActual = new DateTimeImmutable($fechaPrestamo);
            $fechaHabilitacion = new DateTimeImmutable($pagoPenalizado->fecha_habilitacion);

            if ($fechaActual < $fechaHabilitacion) {
                return [
                    'valido' => false,
                    'prestamo_id' => null,
                    'libro_estado' => null,
                    'fecha_devolucion' => null,
                    'puede_prestar' => false,
                    'mensaje' => 'Usuario penalizado; no esta habilitado para registrar prestamos.',
                ];
            }
        }

        $resultado = $this->registrarPrestamo($userId, $libroId, $rol, $fechaPrestamo);
        $resultado['puede_prestar'] = $resultado['valido'];

        return $resultado;
    }

    /**
     * @return array{valido: false, prestamo_id: null, libro_estado: null, fecha_devolucion: null, mensaje: string}
     */
    private function respuestaPrestamoInvalido(string $mensaje): array
    {
        return [
            'valido' => false,
            'prestamo_id' => null,
            'libro_estado' => null,
            'fecha_devolucion' => null,
            'mensaje' => $mensaje,
        ];
    }
}
