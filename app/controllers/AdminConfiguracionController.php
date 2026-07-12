<?php
require_once __DIR__ . '/../models/ConfiguracionPago.php';
require_once __DIR__ . '/../helpers/Csrf.php';

// Controlador: consulta y versionado seguro de la configuración de pagos.
class AdminConfiguracionController
{
    private ConfiguracionPago $modelo;
    public function __construct(mysqli $conexion) { $this->modelo = new ConfiguracionPago($conexion); }

    public function index(): void
    {
        $this->requerirAdmin();
        $pagina = filter_var($_GET['pagina'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
        $porPagina = 10;
        $total = $this->modelo->contarHistorial();
        $paginas = max(1, (int) ceil($total / $porPagina));
        $pagina = min($pagina, $paginas);
        $configuracion = $this->modelo->obtenerActiva();
        $historial = $this->modelo->listarHistorial($porPagina, ($pagina - 1) * $porPagina);
        $csrfToken = Csrf::token();
        $rutaAdminActiva = 'admin-configuracion';
        $flash = $this->flashObtener();
        require __DIR__ . '/../views/admin/configuracion/index.php';
    }

    public function guardar(): void
    {
        $this->requerirAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); header('Allow: POST'); exit; }
        if (!Csrf::validar($_POST['csrf_token'] ?? null)) { $this->flash('error', 'Token de seguridad inválido.'); $this->redirigir(); }
        if (!$this->modelo->existeConfiguracionActiva()) { $this->flash('error', 'No existe una configuración activa.'); $this->redirigir(); }

        $datos = [];
        foreach (['mensualidad', 'aportacion_tsu', 'recargo_vencimiento'] as $campo) {
            $entrada = is_string($_POST[$campo] ?? null) ? trim($_POST[$campo]) : '';
            $valor = filter_var($entrada, FILTER_VALIDATE_FLOAT);
            if (!preg_match('/^\d{1,5}(?:\.\d{1,2})?$/', $entrada) || $valor === false || $valor < 0 || $valor > 99999.99) { $this->flash('error', 'Los importes no pueden ser negativos.'); $this->redirigir(); }
            $datos[$campo] = round((float) $valor, 2);
        }
        $dia = filter_var($_POST['dia_limite'] ?? null, FILTER_VALIDATE_INT);
        if ($dia === false || $dia < 1 || $dia > 28) { $this->flash('error', 'El día límite debe estar entre 1 y 28.'); $this->redirigir(); }
        $datos['dia_limite'] = (int) $dia;
        $fecha = is_string($_POST['vigente_desde'] ?? null) ? $_POST['vigente_desde'] : '';
        $objetoFecha = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
        if (!$objetoFecha || $objetoFecha->format('Y-m-d') !== $fecha) { $this->flash('error', 'La fecha de vigencia no es válida.'); $this->redirigir(); }
        $datos['vigente_desde'] = $fecha;
        $motivo = trim(is_string($_POST['motivo_cambio'] ?? null) ? $_POST['motivo_cambio'] : '');
        $longitud = function_exists('mb_strlen') ? mb_strlen($motivo, 'UTF-8') : strlen($motivo);
        if ($longitud < 10 || $longitud > 255) { $this->flash('error', 'El motivo del cambio es obligatorio.'); $this->redirigir(); }
        $datos['motivo_cambio'] = $motivo;

        $id = $this->modelo->crearNuevaVersion($datos, (int) $_SESSION['id_usuario']);
        $this->flash($id ? 'exito' : 'error', $id ? 'Configuración actualizada correctamente.' : 'No fue posible actualizar la configuración.');
        $this->redirigir();
    }

    private function requerirAdmin(): void { if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] ?? '') !== 'Administrador') { header('Location: ' . BASE_URL . '/index.php?ruta=login-admin'); exit; } }
    private function flash(string $tipo, string $mensaje): void { $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje]; }
    private function flashObtener(): ?array { $flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return is_array($flash) ? $flash : null; }
    private function redirigir(): never { header('Location: ' . BASE_URL . '/index.php?ruta=admin-configuracion'); exit; }
}
