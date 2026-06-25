<?php

namespace App\Services;

class RegistroService
{
    private const ROLES = [
        '4' => [
            'rol' => 'estudiante',
            'gafete' => '444',
        ],
        '7' => [
            'rol' => 'docente',
            'gafete' => '443',
        ],
    ];

    /**
     * Valida que el DNI, codigo institucional y gafete correspondan al mismo rol.
     *
     * @return array{valido: bool, rol: string|null, mensaje: string}
     */
    public function validarRegistro(string $dni, string $codigoInstitucion, string $codigoGafete): array
    {
        if (!preg_match('/^\d{8}$/', $dni)) {
            return $this->respuestaInvalida('El DNI debe tener 8 digitos.');
        }

        if (!preg_match('/^\d{9}$/', $codigoInstitucion)) {
            return $this->respuestaInvalida('El codigo institucional debe tener 9 digitos.');
        }

        $prefijoRol = substr($codigoInstitucion, 0, 1);
        $dniEnCodigo = substr($codigoInstitucion, 1);

        if (!array_key_exists($prefijoRol, self::ROLES)) {
            return $this->respuestaInvalida('El prefijo del codigo institucional no corresponde a un rol valido.');
        }

        if ($dniEnCodigo !== $dni) {
            return $this->respuestaInvalida('El DNI no coincide con el codigo institucional.');
        }

        $datosRol = self::ROLES[$prefijoRol];

        if ($codigoGafete !== $datosRol['gafete']) {
            return $this->respuestaInvalida('El gafete fisico no corresponde al rol institucional.');
        }

        return [
            'valido' => true,
            'rol' => $datosRol['rol'],
            'mensaje' => 'Registro valido.',
        ];
    }

    /**
     * @return array{valido: false, rol: null, mensaje: string}
     */
    private function respuestaInvalida(string $mensaje): array
    {
        return [
            'valido' => false,
            'rol' => null,
            'mensaje' => $mensaje,
        ];
    }
}
