<?php
require_once __DIR__ . '/../../app/helpers/PeriodoHelper.php';

// Helper API: sesión, validaciones comunes y respuestas JSON.
function iniciarApi(): void
{
    ini_set('display_errors', '0');
    header('Content-Type: application/json; charset=utf-8');

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function responderJson(array $datos, int $codigo = 200): never
{
    http_response_code($codigo);
    $json = json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false) {
        http_response_code(500);
        echo '{"exito":false,"mensaje":"No fue posible generar la respuesta."}';
        exit;
    }

    echo $json;
    exit;
}

function responderError(string $mensaje, int $codigo): never
{
    responderJson(['exito' => false, 'mensaje' => $mensaje], $codigo);
}

function requerirMetodo(string $metodo): void
{
    if ($_SERVER['REQUEST_METHOD'] !== $metodo) {
        header('Allow: ' . $metodo);
        responderError('Método HTTP no permitido.', 405);
    }
}

function obtenerIdAlumnoAutenticado(): int
{
    if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
        responderError('Se requiere una sesión válida de alumno.', 401);
    }

    return (int) $_SESSION['id_alumno'];
}

function leerJson(): array
{
    $contenido = file_get_contents('php://input');

    if ($contenido === false || trim($contenido) === '') {
        responderError('El cuerpo JSON es obligatorio.', 400);
    }

    $datos = json_decode($contenido, true);

    if (!is_array($datos) || json_last_error() !== JSON_ERROR_NONE) {
        responderError('El cuerpo JSON no es válido.', 400);
    }

    return $datos;
}

function serializarAdeudo(array $adeudo): array
{
    return [
        'id_adeudo' => (int) $adeudo['id_adeudo'],
        'periodo' => $adeudo['periodo'],
        'periodo_texto' => PeriodoHelper::formatear($adeudo['periodo'] ?? null),
        'mensualidad' => (float) $adeudo['mensualidad'],
        'aportacion_tsu' => (float) $adeudo['aportacion_tsu'],
        'atraso' => (float) ($adeudo['atraso'] ?? 0),
        'recargo' => (float) $adeudo['recargo'],
        'total' => (float) $adeudo['total'],
        'estado' => (string) $adeudo['estado'],
    ];
}
