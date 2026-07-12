<?php
// Modelo: indicadores reales del panel administrativo.
class AdminDashboard
{
    private mysqli $conexion;

    public function __construct(mysqli $conexion)
    {
        $this->conexion = $conexion;
    }

    public function contarAlumnos(): int
    {
        return $this->obtenerEntero('SELECT COUNT(*) AS total FROM alumnos');
    }

    public function contarAdeudosPendientes(): int
    {
        $estado = 'Pendiente';
        $stmt = $this->conexion->prepare('SELECT COUNT(*) AS total FROM adeudos WHERE estado = ?');
        $stmt->bind_param('s', $estado);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();

        return (int) ($fila['total'] ?? 0);
    }

    public function contarPagos(): int
    {
        return $this->obtenerEntero('SELECT COUNT(*) AS total FROM pagos');
    }

    public function obtenerMontoRecaudado(): float
    {
        $resultado = $this->conexion->query('SELECT COALESCE(SUM(total_pagado), 0) AS total FROM pagos WHERE estado_validacion = "Validado"');
        $fila = $resultado->fetch_assoc();

        return (float) ($fila['total'] ?? 0);
    }

    private function obtenerEntero(string $sql): int
    {
        $resultado = $this->conexion->query($sql);
        $fila = $resultado->fetch_assoc();

        return (int) ($fila['total'] ?? 0);
    }
}
