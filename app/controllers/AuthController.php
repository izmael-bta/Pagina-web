<?php
require_once __DIR__ . '/../models/Alumno.php';

// Controlador: autenticación, contraseña inicial y sesión del alumno.
class AuthController
{
    private Alumno $alumnoModelo;

    public function __construct(mysqli $conexion)
    {
        $this->alumnoModelo = new Alumno($conexion);
    }

    public function mostrarLoginAlumno(): void
    {
        $this->redirigirSiAutenticado();
        $rutaActiva = 'login';
        $flash = $this->obtenerFlash();

        require __DIR__ . '/../views/auth/login.php';
    }

    // Compatibilidad con el nombre usado antes de agregar contraseña.
    public function login(): void
    {
        $this->mostrarLoginAlumno();
    }

    public function autenticarAlumno(): void
    {
        $this->redirigirSiAutenticado();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->establecerFlash('informacion', 'Utiliza el formulario para iniciar sesión.');
            $this->redirigir('login');
        }

        $matricula = trim($_POST['matricula'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($matricula === '' || $password === '') {
            $this->establecerFlash('error', 'Completa la matrícula y la contraseña.');
            $this->redirigir('login');
        }

        $alumno = $this->alumnoModelo->buscarPorMatricula($matricula);

        if ($alumno === null || ($alumno['estado'] ?? 'Activo') !== 'Activo') {
            $this->establecerFlash('error', 'Matrícula o contraseña incorrectas.');
            $this->redirigir('login');
        }

        $passwordHash = $this->alumnoModelo->obtenerPasswordHashPorMatricula($matricula);

        if ($passwordHash === null) {
            $this->establecerFlash(
                'advertencia',
                'Aún no has creado una contraseña. Utiliza la opción Crear contraseña.'
            );
            $this->redirigir('login');
        }

        if (!password_verify($password, $passwordHash)) {
            $this->establecerFlash('error', 'Matrícula o contraseña incorrectas.');
            $this->redirigir('login');
        }

        session_regenerate_id(true);
        $_SESSION['id_alumno'] = (int) $alumno['id_alumno'];
        $_SESSION['matricula'] = $alumno['matricula'];
        $_SESSION['nombre'] = $alumno['nombre'];
        $_SESSION['rol'] = 'alumno';

        $this->redirigir('alumno');
    }

    public function mostrarCrearPassword(): void
    {
        $this->redirigirSiAutenticado();
        $rutaActiva = 'login';
        $flash = $this->obtenerFlash();

        require __DIR__ . '/../views/auth/crear_password.php';
    }

    public function guardarPasswordAlumno(): void
    {
        $this->redirigirSiAutenticado();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->establecerFlash('informacion', 'Utiliza el formulario para crear tu contraseña.');
            $this->redirigir('crear-password');
        }

        $matricula = trim($_POST['matricula'] ?? '');
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $password = $_POST['password'] ?? '';
        $confirmacion = $_POST['confirmar_password'] ?? '';

        if ($matricula === '' || $correo === '' || $password === '' || $confirmacion === '') {
            $this->establecerFlash('error', 'Todos los campos son obligatorios.');
            $this->redirigir('crear-password');
        }

        if (!hash_equals($password, $confirmacion)) {
            $this->establecerFlash('error', 'Las contraseñas no coinciden.');
            $this->redirigir('crear-password');
        }

        if (!$this->passwordEsSegura($password)) {
            $this->establecerFlash(
                'advertencia',
                'La contraseña es insegura. Usa al menos 8 caracteres, incluyendo mayúscula, minúscula, número y carácter especial. Se recomiendan 12 caracteres o más.'
            );
            $this->redirigir('crear-password');
        }

        $alumno = $this->alumnoModelo->buscarPorMatriculaYCorreo($matricula, $correo);

        if ($alumno === null) {
            $this->establecerFlash('error', 'No fue posible verificar los datos del alumno.');
            $this->redirigir('crear-password');
        }

        if ($this->alumnoModelo->tienePassword((int) $alumno['id_alumno'])) {
            $this->establecerFlash(
                'advertencia',
                'Este alumno ya tiene una contraseña. Inicia sesión o utiliza posteriormente el proceso de recuperación.'
            );
            $this->redirigir('crear-password');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if ($passwordHash === false) {
            $this->establecerFlash('error', 'No fue posible crear la contraseña. Intenta nuevamente.');
            $this->redirigir('crear-password');
        }

        if (!$this->alumnoModelo->guardarPasswordHash((int) $alumno['id_alumno'], $passwordHash)) {
            $this->establecerFlash('error', 'No fue posible crear la contraseña. Intenta nuevamente.');
            $this->redirigir('crear-password');
        }

        $this->establecerFlash('exito', 'Contraseña creada correctamente. Ya puedes iniciar sesión.');
        $this->redirigir('login');
    }

    public function loginAdmin(): void
    {
        $this->redirigirSiAutenticado();
        $rutaActiva = 'login-admin';
        require __DIR__ . '/../views/auth/login-admin.php';
    }

    public function loginQa(): void
    {
        $this->redirigirSiAutenticado();
        $rutaActiva = 'login-qa';
        require __DIR__ . '/../views/auth/login-qa.php';
    }

    public function cerrarSesion(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $parametros = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?ruta=login');
        exit;
    }

    public function salir(): void
    {
        $this->cerrarSesion();
    }

    private function passwordEsSegura(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/[0-9]/', $password) === 1
            && preg_match('/[^A-Za-z0-9]/', $password) === 1;
    }

    private function establecerFlash(string $tipo, string $mensaje): void
    {
        $_SESSION['flash'] = ['tipo' => $tipo, 'mensaje' => $mensaje];
    }

    private function obtenerFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        return $flash;
    }

    private function redirigirSiAutenticado(): void
    {
        if (isset($_SESSION['id_alumno']) && ($_SESSION['rol'] ?? '') === 'alumno') {
            $this->redirigir('alumno');
        }
    }

    private function redirigir(string $ruta): void
    {
        header('Location: ' . BASE_URL . '/index.php?ruta=' . $ruta);
        exit;
    }
}
