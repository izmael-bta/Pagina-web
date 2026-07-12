<?php
// Modelo: acceso a los usuarios internos del portal.
class Usuario
{
    private mysqli $conexion;

    public function __construct(mysqli $conexion)
    {
        $this->conexion = $conexion;
    }

    public function buscarPorCorreo(string $correo): ?array
    {
        $stmt = $this->conexion->prepare(
            'SELECT id_usuario, nombre, correo, password_hash, rol, estado
             FROM usuarios
             WHERE correo = ?
             LIMIT 1'
        );
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        return $usuario ?: null;
    }

    public function buscarActivoPorCorreo(string $correo): ?array
    {
        $estado = 'Activo';
        $stmt = $this->conexion->prepare(
            'SELECT id_usuario, nombre, correo, password_hash, rol, estado
             FROM usuarios
             WHERE correo = ? AND estado = ?
             LIMIT 1'
        );
        $stmt->bind_param('ss', $correo, $estado);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        return $usuario ?: null;
    }
}
