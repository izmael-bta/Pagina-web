<?php
// Modelo: acceso a los adeudos escolares.
class Adeudo
{
    private mysqli $conexion;

    public function __construct(mysqli $conexion)
    {
        $this->conexion = $conexion;
    }

    public function obtenerUltimoPorAlumno(int $idAlumno, bool $bloquear = false): ?array
    {
        $sql = 'SELECT id_adeudo, id_alumno, periodo, fecha_limite, mensualidad, aportacion_tsu, atraso, recargo, total, estado
                FROM adeudos
                WHERE id_alumno = ?
                ORDER BY periodo DESC, id_adeudo DESC
                LIMIT 1';

        if ($bloquear) {
            $sql .= ' FOR UPDATE';
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $idAlumno);
        $stmt->execute();
        $adeudo = $stmt->get_result()->fetch_assoc();

        return $adeudo ?: null;
    }

    public function obtenerTodosPorAlumno(int $idAlumno): array
    {
        $stmt = $this->conexion->prepare(
            'SELECT id_adeudo, id_alumno, periodo, fecha_limite, mensualidad, aportacion_tsu, atraso, recargo, total, estado
             FROM adeudos
             WHERE id_alumno = ?
             ORDER BY periodo DESC, id_adeudo DESC'
        );
        $stmt->bind_param('i', $idAlumno);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function registrarMensual(
        int $idAlumno,
        int $mes,
        int $anio,
        float $mensualidad,
        float $aportacionTsu,
        float $atraso,
        float $recargo,
        float $montoRecargoVencimiento,
        int $diaLimite,
        string $estado = 'Pendiente'
    ): bool {
        if (!checkdate($mes, 1, $anio)) {
            throw new InvalidArgumentException('El mes o año del periodo no es válido.');
        }

        if ($mensualidad < 0 || $aportacionTsu < 0 || $atraso < 0 || $recargo < 0) {
            throw new InvalidArgumentException('Los importes del adeudo no pueden ser negativos.');
        }

        $periodo = sprintf('%04d-%02d-01', $anio, $mes);
        if ($diaLimite < 1 || $diaLimite > 28 || $montoRecargoVencimiento < 0) {
            throw new InvalidArgumentException('La configuración del adeudo no es válida.');
        }
        $fechaLimite = sprintf('%s-%02d', substr($periodo, 0, 7), $diaLimite);
        // total = mensualidad + aportacion_tsu + atraso + recargo.
        $total = round($mensualidad + $aportacionTsu + $atraso + $recargo, 2);
        $stmt = $this->conexion->prepare(
            'INSERT INTO adeudos
             (id_alumno, periodo, fecha_limite, mensualidad, aportacion_tsu, atraso, recargo, monto_recargo_vencimiento, total, estado)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'issdddddds',
            $idAlumno,
            $periodo,
            $fechaLimite,
            $mensualidad,
            $aportacionTsu,
            $atraso,
            $recargo,
            $montoRecargoVencimiento,
            $total,
            $estado
        );

        return $stmt->execute();
    }

    public function marcarComoPagado(int $idAdeudo): bool
    {
        $estado = 'Pagado';
        $stmt = $this->conexion->prepare('UPDATE adeudos SET estado = ? WHERE id_adeudo = ?');
        $stmt->bind_param('si', $estado, $idAdeudo);

        return $stmt->execute();
    }

    public function actualizarRecargosVencidos(): int
    {
        $stmt = $this->conexion->prepare(
            'UPDATE adeudos
             SET recargo = monto_recargo_vencimiento,
                 total = mensualidad + aportacion_tsu + atraso + monto_recargo_vencimiento
             WHERE estado = "Pendiente"
               AND fecha_limite IS NOT NULL
               AND CURDATE() > fecha_limite
               AND recargo = 0.00'
        );
        $stmt->execute();

        return $stmt->affected_rows;
    }

    public function actualizarRecargoDeAdeudo(int $idAdeudo): bool
    {
        $stmt = $this->conexion->prepare(
            'UPDATE adeudos
             SET recargo = monto_recargo_vencimiento,
                 total = mensualidad + aportacion_tsu + atraso + monto_recargo_vencimiento
             WHERE id_adeudo = ?
               AND estado = "Pendiente"
               AND fecha_limite IS NOT NULL
               AND CURDATE() > fecha_limite
               AND recargo = 0.00'
        );
        $stmt->bind_param('i', $idAdeudo);

        return $stmt->execute();
    }

    public function listarParaAdministrador(string $busqueda = '', string $periodo = '', string $estado = '', int $limite = 10, int $offset = 0): array
    {
        $patron = '%' . $busqueda . '%';
        $stmt = $this->conexion->prepare(
            'SELECT d.id_adeudo,d.id_alumno,a.matricula,a.nombre,a.correo,d.periodo,d.fecha_limite,d.mensualidad,
                    d.aportacion_tsu,d.atraso,d.recargo,d.total,d.estado
             FROM adeudos d INNER JOIN alumnos a ON a.id_alumno=d.id_alumno
             WHERE (?="" OR a.matricula LIKE ? OR a.nombre LIKE ? OR a.correo LIKE ?)
               AND (?="" OR DATE_FORMAT(d.periodo,"%Y-%m")=?) AND (?="" OR d.estado=?)
             ORDER BY d.periodo DESC,d.id_adeudo DESC LIMIT ? OFFSET ?'
        );
        $stmt->bind_param('ssssssssii', $busqueda,$patron,$patron,$patron,$periodo,$periodo,$estado,$estado,$limite,$offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function contarParaAdministrador(string $busqueda = '', string $periodo = '', string $estado = ''): int
    {
        $patron = '%' . $busqueda . '%';
        $stmt = $this->conexion->prepare(
            'SELECT COUNT(*) total FROM adeudos d INNER JOIN alumnos a ON a.id_alumno=d.id_alumno
             WHERE (?="" OR a.matricula LIKE ? OR a.nombre LIKE ? OR a.correo LIKE ?)
               AND (?="" OR DATE_FORMAT(d.periodo,"%Y-%m")=?) AND (?="" OR d.estado=?)'
        );
        $stmt->bind_param('ssssssss', $busqueda,$patron,$patron,$patron,$periodo,$periodo,$estado,$estado);
        $stmt->execute();
        return (int) ($stmt->get_result()->fetch_assoc()['total'] ?? 0);
    }

    public function buscarDetallePorId(int $idAdeudo): ?array
    {
        $stmt = $this->conexion->prepare(
            'SELECT d.id_adeudo,d.id_alumno,a.matricula,a.nombre,a.correo,d.periodo,d.fecha_limite,d.mensualidad,
                    d.aportacion_tsu,d.atraso,d.recargo,d.total,d.estado
             FROM adeudos d INNER JOIN alumnos a ON a.id_alumno=d.id_alumno WHERE d.id_adeudo=?'
        );
        $stmt->bind_param('i',$idAdeudo); $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function existeParaAlumnoYPeriodo(int $idAlumno, string $periodo, ?int $excluirId = null): bool
    {
        $id = $excluirId ?? 0;
        $stmt = $this->conexion->prepare('SELECT 1 FROM adeudos WHERE id_alumno=? AND periodo=? AND (?=0 OR id_adeudo<>?) LIMIT 1');
        $stmt->bind_param('isii',$idAlumno,$periodo,$id,$id); $stmt->execute();
        return $stmt->get_result()->fetch_assoc() !== null;
    }

    public function crear(array $datos): int|false
    {
        $estado='Pendiente';
        $fechaLimite=sprintf('%s-%02d',substr($datos['periodo'],0,7),(int)$datos['dia_limite']);
        $stmt=$this->conexion->prepare('INSERT INTO adeudos (id_alumno,periodo,fecha_limite,mensualidad,aportacion_tsu,atraso,recargo,monto_recargo_vencimiento,total,estado) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('issdddddds',$datos['id_alumno'],$datos['periodo'],$fechaLimite,$datos['mensualidad'],$datos['aportacion_tsu'],$datos['atraso'],$datos['recargo'],$datos['monto_recargo_vencimiento'],$datos['total'],$estado);
        return $stmt->execute() ? (int)$this->conexion->insert_id : false;
    }

    public function actualizarPendiente(int $idAdeudo, array $datos): bool
    {
        $estado='Pendiente';
        $stmt=$this->conexion->prepare('UPDATE adeudos SET periodo=?,mensualidad=?,aportacion_tsu=?,atraso=?,recargo=?,total=? WHERE id_adeudo=? AND estado=?');
        $stmt->bind_param('sdddddis',$datos['periodo'],$datos['mensualidad'],$datos['aportacion_tsu'],$datos['atraso'],$datos['recargo'],$datos['total'],$idAdeudo,$estado);
        $stmt->execute();
        return $stmt->affected_rows === 1;
    }
}
