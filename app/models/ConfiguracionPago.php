<?php
// Modelo: versiones históricas de la configuración usada por nuevos adeudos.
class ConfiguracionPago
{
    public function __construct(private mysqli $conexion) {}

    public function obtenerActiva(): ?array
    {
        $resultado = $this->conexion->query(
            'SELECT cp.*, u.nombre AS creada_por_nombre
             FROM configuracion_pagos cp
             LEFT JOIN usuarios u ON u.id_usuario = cp.creada_por
             WHERE cp.activa = 1
             ORDER BY cp.id_configuracion DESC LIMIT 1'
        );
        return $resultado->fetch_assoc() ?: null;
    }

    public function listarHistorial(int $limite = 10, int $offset = 0): array
    {
        $stmt = $this->conexion->prepare(
            'SELECT cp.*, u.nombre AS creada_por_nombre
             FROM configuracion_pagos cp
             LEFT JOIN usuarios u ON u.id_usuario = cp.creada_por
             ORDER BY cp.id_configuracion DESC LIMIT ? OFFSET ?'
        );
        $stmt->bind_param('ii', $limite, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function contarHistorial(): int
    {
        return (int) ($this->conexion->query('SELECT COUNT(*) total FROM configuracion_pagos')->fetch_assoc()['total'] ?? 0);
    }

    public function existeConfiguracionActiva(): bool
    {
        return $this->conexion->query('SELECT 1 FROM configuracion_pagos WHERE activa = 1 LIMIT 1')->fetch_assoc() !== null;
    }

    public function crearNuevaVersion(array $datos, int $idAdministrador): int|false
    {
        $this->conexion->begin_transaction();
        try {
            $actual = $this->conexion->query(
                'SELECT id_configuracion FROM configuracion_pagos WHERE activa = 1 ORDER BY id_configuracion DESC FOR UPDATE'
            )->fetch_all(MYSQLI_ASSOC);
            if (count($actual) !== 1) throw new RuntimeException('Configuración activa inválida.');

            $desactivar = $this->conexion->prepare('UPDATE configuracion_pagos SET activa = 0 WHERE id_configuracion = ? AND activa = 1');
            $idActual = (int) $actual[0]['id_configuracion'];
            $desactivar->bind_param('i', $idActual);
            $desactivar->execute();
            if ($desactivar->affected_rows !== 1) throw new RuntimeException('No se pudo cerrar la versión anterior.');

            $insertar = $this->conexion->prepare(
                'INSERT INTO configuracion_pagos
                 (mensualidad, aportacion_tsu, recargo_vencimiento, dia_limite, vigente_desde, activa, motivo_cambio, creada_por)
                 VALUES (?, ?, ?, ?, ?, 1, ?, ?)'
            );
            $insertar->bind_param(
                'dddissi',
                $datos['mensualidad'], $datos['aportacion_tsu'], $datos['recargo_vencimiento'],
                $datos['dia_limite'], $datos['vigente_desde'], $datos['motivo_cambio'], $idAdministrador
            );
            $insertar->execute();
            $id = (int) $this->conexion->insert_id;
            if ($id < 1) throw new RuntimeException('No se pudo crear la versión.');
            $this->conexion->commit();
            return $id;
        } catch (Throwable $e) {
            $this->conexion->rollback();
            return false;
        }
    }
}
