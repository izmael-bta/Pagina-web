<?php
// Helper compartido: formato legible de periodos mensuales.
class PeriodoHelper
{
    public static function formatear(?string $periodo): string
    {
        if ($periodo === null || trim($periodo) === '') {
            return 'Periodo no especificado';
        }

        $fecha = DateTimeImmutable::createFromFormat('!Y-m-d', $periodo);
        $errores = DateTimeImmutable::getLastErrors();

        if ($fecha === false || ($errores !== false && ($errores['warning_count'] > 0 || $errores['error_count'] > 0))) {
            return 'Periodo no especificado';
        }

        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return $meses[(int) $fecha->format('n')] . ' ' . $fecha->format('Y');
    }
}
