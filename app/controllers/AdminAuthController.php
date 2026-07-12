<?php
require_once __DIR__ . '/../models/Usuario.php';

// Controlador: autenticación y sesión exclusiva del administrador.
class AdminAuthController
{
    private Usuario $usuarioModelo;

    public function __construct(mysqli $conexion)
    {
        $this->usuarioModelo = new Usuario($conexion);
    }

    public function mostrarLogin(): void
    {
        if ($this->administradorAutenticado()) {
            $this->redirigir('admin-dashboard');
        }

        $rutaActiva = 'login-admin';
        $flash = $this->obtenerFlash();
        require __DIR__ . '/../views/auth/login-admin.php';
    }

    public function autenticar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->establecerFlash('informacion', 'Utiliza el formulario para iniciar sesión.');
            $this->redirigir('login-admin');
        }

        $correo = strtolower(trim(is_string($_POST['correo'] ?? null) ? $_POST['correo'] : ''));
        $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

        if ($correo === '' || $password === '' || filter_var($correo, FILTER_VALIDATE_EMAIL) === false) {
            $this->establecerFlash('error', 'Correo o contraseña incorrectos.');
            $this->redirigir('login-admin');
        }

        try {
            $usuario = $this->usuarioModelo->buscarActivoPorCorreo($correo);
        } catch (Throwable $error) {
            $this->establecerFlash('error', 'No fue posible iniciar sesión. Intenta nuevamente.');
            $this->redirigir('login-admin');
        }

        if ($usuario === null
            || $usuario['rol'] !== 'Administrador'
            || !password_verify($password, $usuario['password_hash'])) {
            $this->establecerFlash('error', 'Correo o contraseña incorrectos.');
            $this->redirigir('login-admin');
        }

        session_regenerate_id(true);
        unset($_SESSION['id_alumno'], $_SESSION['matricula'], $_SESSION['nombre']);
        $_SESSION['id_usuario'] = (int) $usuario['id_usuario'];
        $_SESSION['nombre_usuario'] = $usuario['nombre'];
        $_SESSION['correo_usuario'] = $usuario['correo'];
        $_SESSION['rol'] = 'Administrador';
        $this->redirigir('admin-dashboard');
    }

    public function dashboard(): void
    {
        if (!$this->administradorAutenticado()) {
            $this->establecerFlash('advertencia', 'Inicia sesión como administrador para continuar.');
            $this->redirigir('login-admin');
        }

        $nombreAdministrador = (string) $_SESSION['nombre_usuario'];
        $flash = $this->obtenerFlash();
        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function cerrarSesion(): void
    {
        if (!$this->administradorAutenticado()) {
            $this->establecerFlash('informacion', 'Inicia sesión como administrador para continuar.');
            $this->redirigir('login-admin');
        }

        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['flash'] = ['tipo' => 'exito', 'mensaje' => 'Sesión administrativa cerrada correctamente.'];
        $this->redirigir('login-admin');
    }

    private function administradorAutenticado(): bool
    {
        return isset($_SESSION['id_usuario']) && ($_SESSION['rol'] ?? '') === 'Administrador';
    }

    private function establecerFlash(string $tipo, string $mensaje): void
    {
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
    }

    private function obtenerFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return is_array($flash) ? $flash : null;
    }

    private function redirigir(string $ruta): never
    {
        header('Location: ' . BASE_URL . '/index.php?ruta=' . $ruta);
        exit;
    }
}
