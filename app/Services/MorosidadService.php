<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use DateTimeImmutable;

class MorosidadService
{
    private const TARIFAS_DIARIAS = [
        'estudiante' => 2.00,
        'docente' => 5.00,
    ];

    /**
     * Calcula la multa acumulada segun rol y dias de retraso.
     *
     * @return array{
     *     valido: bool,
     *     rol: string|null,
     *     dias_retraso: int,
     *     tarifa_diaria: float,
     *     multa_total: float,
     *     estado: string,
     *     mensaje: string
     * }
     */
    public function calcularMulta(string $rol, string $fechaDevolucion, string $fechaActual): array
    {
        if (!$this->rolEsValido($rol)) {
            return [
                'valido' => false,
                'rol' => null,
                'dias_retraso' => 0,
                'tarifa_diaria' => 0.00,
                'multa_total' => 0.00,
                'estado' => 'ERROR',
                'mensaje' => 'Rol no valido para calculo de morosidad.',
            ];
        }

        $diasRetraso = $this->calcularDiasRetraso($fechaDevolucion, $fechaActual);
        $tarifaDiaria = self::TARIFAS_DIARIAS[$rol];
        $multaTotal = $diasRetraso * $tarifaDiaria;

        return [
            'valido' => true,
            'rol' => $rol,
            'dias_retraso' => $diasRetraso,
            'tarifa_diaria' => $tarifaDiaria,
            'multa_total' => $multaTotal,
            'estado' => $diasRetraso > 0 ? 'MOROSO' : 'AL_DIA',
            'mensaje' => 'Multa calculada correctamente.',
        ];
    }

    /**
     * Calcula la multa hasta la fecha de pago y detiene su acumulacion.
     *
     * @return array{
     *     valido: bool,
     *     rol: string|null,
     *     dias_retraso_hasta_pago: int,
     *     multa_pagada: float,
     *     fecha_pago: string,
     *     fecha_actual: string,
     *     multa_actual: float,
     *     multa_sigue_acumulando: bool,
     *     estado: string,
     *     mensaje: string
     * }
     */
    public function calcularMultaDespuesDePago(
        string $rol,
        string $fechaDevolucion,
        string $fechaPago,
        string $fechaActual
    ): array {
        if (!$this->rolEsValido($rol)) {
            return [
                'valido' => false,
                'rol' => null,
                'dias_retraso_hasta_pago' => 0,
                'multa_pagada' => 0.00,
                'fecha_pago' => $fechaPago,
                'fecha_actual' => $fechaActual,
                'multa_actual' => 0.00,
                'multa_sigue_acumulando' => false,
                'estado' => 'ERROR',
                'mensaje' => 'Rol no valido para calculo de morosidad.',
            ];
        }

        $diasRetrasoHastaPago = $this->calcularDiasRetraso($fechaDevolucion, $fechaPago);
        $multaPagada = $diasRetrasoHastaPago * self::TARIFAS_DIARIAS[$rol];

        return [
            'valido' => true,
            'rol' => $rol,
            'dias_retraso_hasta_pago' => $diasRetrasoHastaPago,
            'multa_pagada' => $multaPagada,
            'fecha_pago' => $fechaPago,
            'fecha_actual' => $fechaActual,
            'multa_actual' => $multaPagada,
            'multa_sigue_acumulando' => false,
            'estado' => 'PAGADA',
            'mensaje' => 'La multa fue pagada y dejo de acumularse.',
        ];
    }

    /**
     * Calcula la penalizacion posterior al pago de una multa.
     *
     * @return array{
     *     valido: bool,
     *     fecha_pago: string,
     *     fecha_actual: string,
     *     dias_penalizacion: int,
     *     fecha_habilitacion: string,
     *     dias_restantes: int,
     *     puede_prestar: bool,
     *     estado: string,
     *     mensaje: string
     * }
     */
    public function calcularPenalizacionDespuesDePago(
        string $fechaPago,
        string $fechaActual,
        int $diasPenalizacion = 21
    ): array {
        $pago = new DateTimeImmutable($fechaPago);
        $actual = new DateTimeImmutable($fechaActual);
        $habilitacion = $pago->modify('+' . $diasPenalizacion . ' days');
        $estaPenalizado = $actual < $habilitacion;
        $diasRestantes = $estaPenalizado ? $actual->diff($habilitacion)->days : 0;

        return [
            'valido' => true,
            'fecha_pago' => $fechaPago,
            'fecha_actual' => $fechaActual,
            'dias_penalizacion' => $diasPenalizacion,
            'fecha_habilitacion' => $habilitacion->format('Y-m-d'),
            'dias_restantes' => $diasRestantes,
            'puede_prestar' => !$estaPenalizado,
            'estado' => $estaPenalizado ? 'PENALIZADO' : 'HABILITADO',
            'mensaje' => $estaPenalizado
                ? 'El usuario sigue en periodo de penalizacion.'
                : 'El usuario ya esta habilitado para prestar libros.',
        ];
    }

    /**
     * Registra el pago de multa y deja persistida la penalizacion resultante.
     *
     * @return array{
     *     valido: bool,
     *     pago_id: int|null,
     *     multa_pagada: float,
     *     fecha_pago: string|null,
     *     fecha_habilitacion: string|null,
     *     puede_prestar: bool,
     *     estado: string,
     *     mensaje: string
     * }
     */
    public function registrarPagoDeMulta(
        int $userId,
        int $prestamoId,
        string $rol,
        string $fechaPago,
        string $fechaActual
    ): array {
        $usuario = User::find($userId);

        if (!$usuario) {
            return $this->respuestaPagoInvalido('Usuario no encontrado.');
        }

        $prestamo = Prestamo::find($prestamoId);

        if (!$prestamo) {
            return $this->respuestaPagoInvalido('Prestamo no encontrado.');
        }

        $multa = $this->calcularMultaDespuesDePago(
            rol: $rol,
            fechaDevolucion: $prestamo->fecha_devolucion,
            fechaPago: $fechaPago,
            fechaActual: $fechaActual,
        );

        if (!$multa['valido']) {
            return $this->respuestaPagoInvalido($multa['mensaje']);
        }

        $penalizacion = $this->calcularPenalizacionDespuesDePago(
            fechaPago: $fechaPago,
            fechaActual: $fechaActual,
        );

        $pago = Pago::create([
            'user_id' => $usuario->id,
            'prestamo_id' => $prestamo->id,
            'monto' => $multa['multa_pagada'],
            'fecha_pago' => $fechaPago,
            'fecha_habilitacion' => $penalizacion['fecha_habilitacion'],
            'estado' => $penalizacion['estado'],
            'pagado_en' => $fechaPago,
        ]);

        $prestamo->update(['estado' => $penalizacion['estado']]);

        return [
            'valido' => true,
            'pago_id' => $pago->id,
            'multa_pagada' => $multa['multa_pagada'],
            'fecha_pago' => $fechaPago,
            'fecha_habilitacion' => $penalizacion['fecha_habilitacion'],
            'puede_prestar' => $penalizacion['puede_prestar'],
            'estado' => $penalizacion['estado'],
            'mensaje' => 'Pago de multa registrado correctamente.',
        ];
    }

    /**
     * @return array{valido: false, pago_id: null, multa_pagada: 0.0, fecha_pago: null, fecha_habilitacion: null, puede_prestar: false, estado: string, mensaje: string}
     */
    private function respuestaPagoInvalido(string $mensaje): array
    {
        return [
            'valido' => false,
            'pago_id' => null,
            'multa_pagada' => 0.00,
            'fecha_pago' => null,
            'fecha_habilitacion' => null,
            'puede_prestar' => false,
            'estado' => 'ERROR',
            'mensaje' => $mensaje,
        ];
    }

    private function rolEsValido(string $rol): bool
    {
        return array_key_exists($rol, self::TARIFAS_DIARIAS);
    }

    private function calcularDiasRetraso(string $fechaDevolucion, string $fechaComparacion): int
    {
        $devolucion = new DateTimeImmutable($fechaDevolucion);
        $comparacion = new DateTimeImmutable($fechaComparacion);

        return $comparacion > $devolucion ? $devolucion->diff($comparacion)->days : 0;
    }
}
