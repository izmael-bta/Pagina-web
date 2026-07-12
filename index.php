<?php
// Front Controller: inicia la aplicación y dirige cada solicitud.
session_start();

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/AlumnoController.php';
require_once __DIR__ . '/app/controllers/PagoController.php';
require_once __DIR__ . '/app/controllers/AdminAuthController.php';
require_once __DIR__ . '/app/controllers/AdminDashboardController.php';
require_once __DIR__ . '/app/controllers/AdminAlumnoController.php';
require_once __DIR__ . '/app/controllers/AdminAdeudoController.php';
require_once __DIR__ . '/app/controllers/AdminPagoController.php';
require_once __DIR__ . '/app/controllers/AdminProrrogaController.php';
require_once __DIR__ . '/app/controllers/AdminAclaracionController.php';
require_once __DIR__ . '/app/controllers/AdminConfiguracionController.php';

$directorioBase = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', $directorioBase === '/' ? '' : rtrim($directorioBase, '/'));

$ruta = $_GET['ruta'] ?? 'login';

switch ($ruta) {
    case 'login':
        (new AuthController($conn))->mostrarLoginAlumno();
        break;
    case 'autenticar-alumno':
        (new AuthController($conn))->autenticarAlumno();
        break;
    case 'crear-password':
        (new AuthController($conn))->mostrarCrearPassword();
        break;
    case 'guardar-password':
        (new AuthController($conn))->guardarPasswordAlumno();
        break;
    case 'login-admin':
        (new AdminAuthController($conn))->mostrarLogin();
        break;
    case 'autenticar-admin':
        (new AdminAuthController($conn))->autenticar();
        break;
    case 'admin-dashboard':
        (new AdminDashboardController($conn))->dashboard();
        break;
    case 'admin-alumnos':
        (new AdminAlumnoController($conn))->index();
        break;
    case 'admin-alumno-crear':
        (new AdminAlumnoController($conn))->crear();
        break;
    case 'admin-alumno-guardar':
        (new AdminAlumnoController($conn))->guardar();
        break;
    case 'admin-alumno-editar':
        (new AdminAlumnoController($conn))->editar();
        break;
    case 'admin-alumno-actualizar':
        (new AdminAlumnoController($conn))->actualizar();
        break;
    case 'admin-alumno-desactivar':
        (new AdminAlumnoController($conn))->desactivar();
        break;
    case 'admin-alumno-reactivar':
        (new AdminAlumnoController($conn))->reactivar();
        break;
    case 'admin-adeudos':
        (new AdminAdeudoController($conn))->index();
        break;
    case 'admin-adeudo-crear':
        (new AdminAdeudoController($conn))->crear();
        break;
    case 'admin-adeudo-guardar':
        (new AdminAdeudoController($conn))->guardar();
        break;
    case 'admin-adeudo-ver':
        (new AdminAdeudoController($conn))->ver();
        break;
    case 'admin-adeudo-editar':
        (new AdminAdeudoController($conn))->editar();
        break;
    case 'admin-adeudo-actualizar':
        (new AdminAdeudoController($conn))->actualizar();
        break;
    case 'admin-pagos':
        (new AdminPagoController($conn))->index();
        break;
    case 'admin-pago-ver': (new AdminPagoController($conn))->ver(); break;
    case 'admin-pago-manual': (new AdminPagoController($conn))->crearManual(); break;
    case 'admin-pago-manual-guardar': (new AdminPagoController($conn))->guardarManual(); break;
    case 'admin-pago-validar': (new AdminPagoController($conn))->validar(); break;
    case 'admin-pago-rechazar': (new AdminPagoController($conn))->rechazar(); break;
    case 'admin-prorrogas':
        (new AdminProrrogaController($conn))->index();
        break;
    case 'admin-prorroga-crear': (new AdminProrrogaController($conn))->crear(); break;
    case 'admin-prorroga-guardar': (new AdminProrrogaController($conn))->guardar(); break;
    case 'admin-prorroga-ver': (new AdminProrrogaController($conn))->ver(); break;
    case 'admin-aclaraciones':
        (new AdminAclaracionController($conn))->index();
        break;
    case 'admin-aclaracion-crear': (new AdminAclaracionController($conn))->crear(); break;
    case 'admin-aclaracion-guardar': (new AdminAclaracionController($conn))->guardar(); break;
    case 'admin-aclaracion-ver': (new AdminAclaracionController($conn))->ver(); break;
    case 'admin-aclaracion-revision': (new AdminAclaracionController($conn))->iniciarRevision(); break;
    case 'admin-aclaracion-resolver': (new AdminAclaracionController($conn))->resolver(); break;
    case 'admin-aclaracion-rechazar': (new AdminAclaracionController($conn))->rechazar(); break;
    case 'admin-configuracion':
        (new AdminConfiguracionController($conn))->index();
        break;
    case 'admin-configuracion-guardar':
        (new AdminConfiguracionController($conn))->guardar();
        break;
    case 'admin-reportes':
        (new AdminDashboardController($conn))->modulo('admin-reportes', 'Reportes');
        break;
    case 'admin-estadisticas':
        (new AdminDashboardController($conn))->modulo('admin-estadisticas', 'Estadísticas');
        break;
    case 'admin-salir':
        (new AdminAuthController($conn))->cerrarSesion();
        break;
    case 'login-qa':
        (new AuthController($conn))->loginQa();
        break;
    case 'alumno':
        (new AlumnoController($conn))->dashboard();
        break;
    case 'registro':
        (new AlumnoController($conn))->registro();
        break;
    case 'pago':
        (new PagoController($conn))->formulario();
        break;
    case 'comprobante':
        (new PagoController($conn))->comprobante();
        break;
    case 'salir':
        (new AuthController($conn))->cerrarSesion();
        break;
    default:
        header('Location: ' . BASE_URL . '/index.php?ruta=login');
        exit;
}
