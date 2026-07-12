<?php
require_once __DIR__ . '/../helpers/respuesta.php';
iniciarApi();

try {
    require_once __DIR__ . '/../../config/conexion.php';
} catch (Throwable $error) {
    responderError('Ocurrió un error interno de conexión.', 500);
}

require_once __DIR__ . '/../../app/models/Alumno.php';

requerirMetodo('GET');
$idAlumno = obtenerIdAlumnoAutenticado();

try {
    $alumno = (new Alumno($conn))->buscarPorId($idAlumno);

    if ($alumno === null) {
        responderError('No se encontró el perfil del alumno.', 404);
    }

    responderJson([
        'exito' => true,
        'alumno' => [
            'id_alumno' => (int) $alumno['id_alumno'],
            'matricula' => (string) $alumno['matricula'],
            'nombre' => (string) $alumno['nombre'],
            'correo' => (string) $alumno['correo'],
            'carrera' => (string) $alumno['carrera'],
            'grupo' => (string) $alumno['grupo'],
        ],
    ]);
} catch (Throwable $error) {
    responderError('Ocurrió un error interno al consultar el perfil.', 500);
}
