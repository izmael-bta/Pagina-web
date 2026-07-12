<?php
// Modelo: acceso a los datos de alumnos.
class Alumno
{
    private mysqli $conexion;

    public function __construct(mysqli $conexion)
    {
        $this->conexion = $conexion;
    }

    public function buscarPorMatricula(string $matricula): ?array
    {
        $stmt = $this->conexion->prepare(
            'SELECT id_alumno, matricula, nombre, correo, carrera, grupo, password_hash, estado FROM alumnos WHERE matricula = ?'
        );
        $stmt->bind_param('s', $matricula);
        $stmt->execute();
        $alumno = $stmt->get_result()->fetch_assoc();

        return $alumno ?: null;
    }

    public function buscarPorMatriculaYCorreo(string $matricula, string $correo): ?array
    {
        $stmt = $this->conexion->prepare(
            'SELECT id_alumno, matricula, nombre, correo, carrera, grupo, password_hash, estado
             FROM alumnos
             WHERE matricula = ? AND LOWER(TRIM(correo)) = ?'
        );
        $stmt->bind_param('ss', $matricula, $correo);
        $stmt->execute();
        $alumno = $stmt->get_result()->fetch_assoc();

        return $alumno ?: null;
    }

    public function tienePassword(int $idAlumno): bool
    {
        $stmt = $this->conexion->prepare('SELECT password_hash FROM alumnos WHERE id_alumno = ?');
        $stmt->bind_param('i', $idAlumno);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();

        return isset($resultado['password_hash']) && $resultado['password_hash'] !== '';
    }

    public function guardarPasswordHash(int $idAlumno, string $passwordHash): bool
    {
        $stmt = $this->conexion->prepare(
            'UPDATE alumnos SET password_hash = ? WHERE id_alumno = ? AND password_hash IS NULL'
        );
        $stmt->bind_param('si', $passwordHash, $idAlumno);
        $stmt->execute();

        return $stmt->affected_rows === 1;
    }

    public function obtenerPasswordHashPorMatricula(string $matricula): ?string
    {
        $stmt = $this->conexion->prepare('SELECT password_hash FROM alumnos WHERE matricula = ?');
        $stmt->bind_param('s', $matricula);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();

        if (!$resultado || $resultado['password_hash'] === null || $resultado['password_hash'] === '') {
            return null;
        }

        return $resultado['password_hash'];
    }

    public function buscarPorId(int $idAlumno): ?array
    {
        $stmt = $this->conexion->prepare(
            'SELECT id_alumno, matricula, nombre, correo, carrera, grupo, estado FROM alumnos WHERE id_alumno = ?'
        );
        $stmt->bind_param('i', $idAlumno);
        $stmt->execute();
        $alumno = $stmt->get_result()->fetch_assoc();

        return $alumno ?: null;
    }

    public function listarParaAdministrador(
        string $busqueda = '',
        string $estado = '',
        int $limite = 10,
        int $offset = 0
    ): array {
        $patron = '%' . $busqueda . '%';
        $stmt = $this->conexion->prepare(
            'SELECT id_alumno, matricula, nombre, correo, carrera, grupo, estado
             FROM alumnos
             WHERE (? = "" OR matricula LIKE ? OR nombre LIKE ? OR correo LIKE ?)
               AND (? = "" OR estado = ?)
             ORDER BY nombre ASC, matricula ASC
             LIMIT ? OFFSET ?'
        );
        $stmt->bind_param('ssssssii', $busqueda, $patron, $patron, $patron, $estado, $estado, $limite, $offset);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function contarParaAdministrador(string $busqueda = '', string $estado = ''): int
    {
        $patron = '%' . $busqueda . '%';
        $stmt = $this->conexion->prepare(
            'SELECT COUNT(*) AS total
             FROM alumnos
             WHERE (? = "" OR matricula LIKE ? OR nombre LIKE ? OR correo LIKE ?)
               AND (? = "" OR estado = ?)'
        );
        $stmt->bind_param('ssssss', $busqueda, $patron, $patron, $patron, $estado, $estado);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();

        return (int) ($fila['total'] ?? 0);
    }

    public function existeMatricula(string $matricula, ?int $excluirId = null): bool
    {
        $id = $excluirId ?? 0;
        $stmt = $this->conexion->prepare(
            'SELECT 1 FROM alumnos WHERE matricula = ? AND (? = 0 OR id_alumno <> ?) LIMIT 1'
        );
        $stmt->bind_param('sii', $matricula, $id, $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc() !== null;
    }

    public function existeCorreo(string $correo, ?int $excluirId = null): bool
    {
        $id = $excluirId ?? 0;
        $stmt = $this->conexion->prepare(
            'SELECT 1 FROM alumnos WHERE LOWER(TRIM(correo)) = ? AND (? = 0 OR id_alumno <> ?) LIMIT 1'
        );
        $stmt->bind_param('sii', $correo, $id, $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc() !== null;
    }

    public function crear(array $datos): int|false
    {
        $passwordHash = null;
        $stmt = $this->conexion->prepare(
            'INSERT INTO alumnos (matricula, nombre, correo, carrera, grupo, password_hash, estado)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'sssssss',
            $datos['matricula'],
            $datos['nombre'],
            $datos['correo'],
            $datos['carrera'],
            $datos['grupo'],
            $passwordHash,
            $datos['estado']
        );

        return $stmt->execute() ? (int) $this->conexion->insert_id : false;
    }

    public function actualizar(int $idAlumno, array $datos): bool
    {
        $stmt = $this->conexion->prepare(
            'UPDATE alumnos SET matricula = ?, nombre = ?, correo = ?, carrera = ?, grupo = ?
             WHERE id_alumno = ?'
        );
        $stmt->bind_param(
            'sssssi',
            $datos['matricula'],
            $datos['nombre'],
            $datos['correo'],
            $datos['carrera'],
            $datos['grupo'],
            $idAlumno
        );

        return $stmt->execute();
    }

    public function cambiarEstado(int $idAlumno, string $estado): bool
    {
        $stmt = $this->conexion->prepare('UPDATE alumnos SET estado = ? WHERE id_alumno = ?');
        $stmt->bind_param('si', $estado, $idAlumno);

        return $stmt->execute();
    }

    public function listarActivosParaSelector(): array
    {
        $estado = 'Activo';
        $stmt = $this->conexion->prepare(
            'SELECT id_alumno, matricula, nombre, correo FROM alumnos WHERE estado = ? ORDER BY nombre ASC'
        );
        $stmt->bind_param('s', $estado);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
