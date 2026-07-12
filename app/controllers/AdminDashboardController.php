<?php
require_once __DIR__ . '/../models/AdminDashboard.php';
require_once __DIR__ . '/../models/Adeudo.php';

// Controlador: protección, indicadores y módulos provisionales administrativos.
class AdminDashboardController
{
    private AdminDashboard $dashboardModelo;
    private Adeudo $adeudoModelo;

    public function __construct(mysqli $conexion)
    {
        $this->dashboardModelo = new AdminDashboard($conexion);
        $this->adeudoModelo = new Adeudo($conexion);
    }

    public function dashboard(): void
    {
        $this->requerirAdministrador();
        $this->adeudoModelo->actualizarRecargosVencidos();
        $nombreAdministrador = (string) $_SESSION['nombre_usuario'];
        $rutaAdminActiva = 'admin-dashboard';

        try {
            $indicadores = [
                'alumnos' => $this->dashboardModelo->contarAlumnos(),
                'adeudos_pendientes' => $this->dashboardModelo->contarAdeudosPendientes(),
                'pagos' => $this->dashboardModelo->contarPagos(),
                'monto_recaudado' => $this->dashboardModelo->obtenerMontoRecaudado(),
            ];
        } catch (Throwable $error) {
            $indicadores = ['alumnos' => 0, 'adeudos_pendientes' => 0, 'pagos' => 0, 'monto_recaudado' => 0.0];
            $mensajeAdmin = 'No fue posible cargar los indicadores en este momento.';
        }

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function modulo(string $ruta, string $titulo): void
    {
        $this->requerirAdministrador();
        $rutaAdminActiva = $ruta;
        $tituloModulo = $titulo;
        require __DIR__ . '/../views/admin/modulo_en_construccion.php';
    }

    private function requerirAdministrador(): void
    {
        if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] ?? '') !== 'Administrador') {
            $_SESSION['flash'] = [
                'tipo' => 'advertencia',
                'mensaje' => 'Inicia sesión como administrador para continuar.',
            ];
            header('Location: ' . BASE_URL . '/index.php?ruta=login-admin');
            exit;
        }
    }
}
