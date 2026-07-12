<?php
require_once __DIR__ . '/../models/Alumno.php';
require_once __DIR__ . '/../helpers/Csrf.php';

// Controlador: consulta y administración lógica de alumnos.
class AdminAlumnoController
{
    private Alumno $alumnoModelo;

    public function __construct(mysqli $conexion)
    {
        $this->alumnoModelo = new Alumno($conexion);
    }

    public function index(): void
    {
        $this->requerirAdministrador();
        $busqueda = trim(is_string($_GET['buscar'] ?? null) ? $_GET['buscar'] : '');
        $estado = is_string($_GET['estado'] ?? null) ? $_GET['estado'] : '';
        $estado = in_array($estado, ['Activo', 'Inactivo'], true) ? $estado : '';
        $pagina = filter_var($_GET['pagina'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
        $porPagina = 10;
        $totalResultados = $this->alumnoModelo->contarParaAdministrador($busqueda, $estado);
        $totalPaginas = max(1, (int) ceil($totalResultados / $porPagina));
        $pagina = min($pagina, $totalPaginas);
        $alumnos = $this->alumnoModelo->listarParaAdministrador(
            $busqueda,
            $estado,
            $porPagina,
            ($pagina - 1) * $porPagina
        );
        $rutaAdminActiva = 'admin-alumnos';
        $csrfToken = Csrf::token();
        $flash = $this->obtenerFlash();
        require __DIR__ . '/../views/admin/alumnos/index.php';
    }

    public function crear(): void
    {
        $this->requerirAdministrador();
        $rutaAdminActiva = 'admin-alumnos';
        $csrfToken = Csrf::token();
        $alumno = [
            'matricula' => '', 'nombre' => '',
            'correo' => '', 'carrera' => 'Desarrollo de Software Multiplataforma', 'grupo' => '',
        ];
        $modoFormulario = 'crear';
        $flash = $this->obtenerFlash();
        require __DIR__ . '/../views/admin/alumnos/crear.php';
    }

    public function guardar(): void
    {
        $this->requerirPostYCsrf('admin-alumno-crear');
        $datos = $this->validarDatos();

        if ($datos === null) {
            $this->redirigir('admin-alumno-crear');
        }

        if ($this->alumnoModelo->existeMatricula($datos['matricula'])) {
            $this->flash('error', 'Matrícula ya registrada.');
            $this->redirigir('admin-alumno-crear');
        }

        if ($this->alumnoModelo->existeCorreo($datos['correo'])) {
            $this->flash('error', 'Correo institucional ya registrado.');
            $this->redirigir('admin-alumno-crear');
        }

        $datos['estado'] = 'Activo';

        try {
            $creado = $this->alumnoModelo->crear($datos);
        } catch (Throwable $error) {
            $creado = false;
        }

        $this->flash(
            $creado !== false ? 'exito' : 'error',
            $creado !== false ? 'Alumno registrado correctamente.' : 'No fue posible completar la operación.'
        );
        $this->redirigir('admin-alumnos');
    }

    public function editar(): void
    {
        $this->requerirAdministrador();
        $idAlumno = $this->obtenerId($_GET['id'] ?? null);
        $alumno = $idAlumno > 0 ? $this->alumnoModelo->buscarPorId($idAlumno) : null;

        if ($alumno === null) {
            $this->flash('error', 'Alumno no encontrado.');
            $this->redirigir('admin-alumnos');
        }

        $rutaAdminActiva = 'admin-alumnos';
        $csrfToken = Csrf::token();
        $modoFormulario = 'editar';
        $flash = $this->obtenerFlash();
        require __DIR__ . '/../views/admin/alumnos/editar.php';
    }

    public function actualizar(): void
    {
        $this->requerirPostYCsrf('admin-alumnos');
        $idAlumno = $this->obtenerId($_POST['id_alumno'] ?? null);
        $existente = $idAlumno > 0 ? $this->alumnoModelo->buscarPorId($idAlumno) : null;

        if ($existente === null) {
            $this->flash('error', 'Alumno no encontrado.');
            $this->redirigir('admin-alumnos');
        }

        $datos = $this->validarDatos();
        if ($datos === null) {
            $this->redirigir('admin-alumno-editar&id=' . $idAlumno);
        }

        if ($this->alumnoModelo->existeMatricula($datos['matricula'], $idAlumno)) {
            $this->flash('error', 'Matrícula ya registrada.');
            $this->redirigir('admin-alumno-editar&id=' . $idAlumno);
        }
        if ($this->alumnoModelo->existeCorreo($datos['correo'], $idAlumno)) {
            $this->flash('error', 'Correo institucional ya registrado.');
            $this->redirigir('admin-alumno-editar&id=' . $idAlumno);
        }

        try {
            $actualizado = $this->alumnoModelo->actualizar($idAlumno, $datos);
        } catch (Throwable $error) {
            $actualizado = false;
        }
        $this->flash(
            $actualizado ? 'exito' : 'error',
            $actualizado ? 'Alumno actualizado correctamente.' : 'No fue posible completar la operación.'
        );
        $this->redirigir('admin-alumnos');
    }

    public function desactivar(): void
    {
        $this->cambiarEstado('Inactivo', 'Alumno desactivado correctamente.');
    }

    public function reactivar(): void
    {
        $this->cambiarEstado('Activo', 'Alumno reactivado correctamente.');
    }

    private function cambiarEstado(string $estado, string $mensaje): void
    {
        $this->requerirPostYCsrf('admin-alumnos');
        $idAlumno = $this->obtenerId($_POST['id_alumno'] ?? null);
        if ($idAlumno <= 0 || $this->alumnoModelo->buscarPorId($idAlumno) === null) {
            $this->flash('error', 'Alumno no encontrado.');
            $this->redirigir('admin-alumnos');
        }

        try {
            $cambiado = $this->alumnoModelo->cambiarEstado($idAlumno, $estado);
        } catch (Throwable $error) {
            $cambiado = false;
        }
        $this->flash($cambiado ? 'exito' : 'error', $cambiado ? $mensaje : 'No fue posible completar la operación.');
        $this->redirigir('admin-alumnos');
    }

    private function validarDatos(): ?array
    {
        $matricula = trim(is_string($_POST['matricula'] ?? null) ? $_POST['matricula'] : '');
        $nombre = trim(is_string($_POST['nombre'] ?? null) ? $_POST['nombre'] : '');
        $carrera = trim(is_string($_POST['carrera'] ?? null) ? $_POST['carrera'] : '');
        $grupo = trim(is_string($_POST['grupo'] ?? null) ? $_POST['grupo'] : '');
        $longitud = static fn(string $valor): int => function_exists('mb_strlen')
            ? mb_strlen($valor, 'UTF-8') : strlen($valor);

        if (!preg_match('/^[0-9]{3,20}$/', $matricula)
            || $nombre === '' || $longitud($nombre) > 100
            || $carrera === '' || $longitud($carrera) > 100
            || $grupo === '' || $longitud($grupo) > 20) {
            $this->flash('error', 'Datos incompletos o inválidos.');
            return null;
        }

        return [
            'matricula' => $matricula,
            'nombre' => $nombre,
            'correo' => strtolower($matricula . '@virtual.utsc.edu.mx'),
            'carrera' => $carrera,
            'grupo' => $grupo,
        ];
    }

    private function requerirPostYCsrf(string $rutaError): void
    {
        $this->requerirAdministrador();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('Método no permitido.');
        }
        if (!Csrf::validar($_POST['csrf_token'] ?? null)) {
            $this->flash('error', 'Token de seguridad inválido.');
            $this->redirigir($rutaError);
        }
    }

    private function requerirAdministrador(): void
    {
        if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] ?? '') !== 'Administrador') {
            $_SESSION['flash'] = ['tipo' => 'advertencia', 'mensaje' => 'Inicia sesión como administrador para continuar.'];
            $this->redirigir('login-admin');
        }
    }

    private function obtenerId(mixed $valor): int
    {
        return filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0;
    }

    private function flash(string $tipo, string $mensaje): void
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
