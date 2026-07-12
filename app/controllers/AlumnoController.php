<?php
require_once __DIR__ . '/../models/Alumno.php';
require_once __DIR__ . '/../models/Adeudo.php';
require_once __DIR__ . '/../models/Pago.php';
require_once __DIR__ . '/../models/Prorroga.php';
require_once __DIR__ . '/../helpers/PeriodoHelper.php';

// Controlador: datos, adeudos y registro demostrativo del estudiante.
class AlumnoController
{
    private Alumno $alumnoModelo;
    private Adeudo $adeudoModelo;
    private Pago $pagoModelo;
    private Prorroga $prorrogaModelo;

    public function __construct(mysqli $conexion)
    {
        $this->alumnoModelo = new Alumno($conexion);
        $this->adeudoModelo = new Adeudo($conexion);
        $this->pagoModelo = new Pago($conexion);
        $this->prorrogaModelo = new Prorroga($conexion);
    }

    public function dashboard(): void
    {
        $idAlumno = $this->obtenerIdAlumno();
        $alumno = $this->alumnoModelo->buscarPorId($idAlumno);

        if ($alumno === null) {
            session_destroy();
            header('Location: ' . BASE_URL . '/index.php?ruta=login');
            exit;
        }

        $this->adeudoModelo->actualizarRecargosVencidos();
        $adeudo = $this->adeudoModelo->obtenerUltimoPorAlumno($idAlumno);
        $totalAdeudo = $adeudo['total'] ?? 0;
        $estadoAdeudo = $adeudo['estado'] ?? 'Sin adeudo';
        $adeudoPagado = strtolower(trim((string) $estadoAdeudo)) === 'pagado';
        $periodoFormateado = PeriodoHelper::formatear($adeudo['periodo'] ?? null);
        $ultimaProrroga = $adeudo ? $this->prorrogaModelo->obtenerUltimaPorAdeudo((int) $adeudo['id_adeudo']) : null;
        $tieneProrroga = $ultimaProrroga !== null;
        $fechaLimite = !empty($adeudo['fecha_limite']) ? DateTimeImmutable::createFromFormat('!Y-m-d', $adeudo['fecha_limite']) : false;
        $fechaLimiteTexto = $fechaLimite ? $fechaLimite->format('d/m/Y') : 'No especificada';
        $estadoProrroga = '';
        $mensajeProrroga = '';
        $avisoRecargo = $adeudo
            && $estadoAdeudo === 'Pendiente'
            && !empty($adeudo['fecha_limite'])
            && new DateTimeImmutable('today') > new DateTimeImmutable($adeudo['fecha_limite'])
            && (float) $adeudo['recargo'] > 0.00;
        if ($tieneProrroga && $adeudoPagado) {
            $estadoProrroga = 'Finalizada';
            $mensajeProrroga = 'Fecha límite aplicada: ' . $fechaLimiteTexto . '.';
        } elseif ($tieneProrroga && $fechaLimite) {
            $estadoProrroga = new DateTimeImmutable('today') <= $fechaLimite ? 'Vigente' : 'Vencida';
            $mensajeProrroga = $estadoProrroga === 'Vigente'
                ? 'Tu prórroga fue autorizada. Puedes realizar el pago hasta el ' . $fechaLimiteTexto . '.'
                : 'La prórroga autorizada venció el ' . $fechaLimiteTexto . '. Consulta tu adeudo actualizado.';
        }
        $folioSesion = is_string($_SESSION['folio'] ?? null) ? $_SESSION['folio'] : '';
        $pagoComprobante = null;

        if ($estadoAdeudo === 'Pagado' && $folioSesion !== '') {
            $pagoComprobante = $this->pagoModelo->buscarPorFolioYAlumno($idAlumno, $folioSesion);
        }

        $existeComprobante = $pagoComprobante !== null;

        require __DIR__ . '/../views/alumno/dashboard.php';
    }

    public function registro(): void
    {
        $estudiante = [
            'nombre' => 'Ismael Bautista Pérez',
            'matricula' => '25906',
            'calificacion' => 90,
        ];
        $aprobo = $estudiante['calificacion'] >= 70;

        require __DIR__ . '/../views/alumno/registro.php';
    }

    private function obtenerIdAlumno(): int
    {
        if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
            header('Location: ' . BASE_URL . '/index.php?ruta=login');
            exit;
        }

        return (int) $_SESSION['id_alumno'];
    }
}
