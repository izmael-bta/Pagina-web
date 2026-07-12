<?php
require_once __DIR__ . '/../helpers/respuesta.php';
iniciarApi();

try {
    require_once __DIR__ . '/../../config/conexion.php';
} catch (Throwable $error) {
    responderError('Ocurrió un error interno de conexión.', 500);
}

require_once __DIR__ . '/../../app/models/Adeudo.php';

requerirMetodo('GET');
$idAlumno = obtenerIdAlumnoAutenticado();

try {
    $adeudo = (new Adeudo($conn))->obtenerUltimoPorAlumno($idAlumno);

    if ($adeudo === null) {
        responderError('No se encontraron adeudos para el alumno.', 404);
    }

    responderJson(['exito' => true, 'adeudo' => serializarAdeudo($adeudo)]);
} catch (Throwable $error) {
    responderError('Ocurrió un error interno al consultar el adeudo.', 500);
}
