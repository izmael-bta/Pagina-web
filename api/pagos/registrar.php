<?php
require_once __DIR__ . '/../helpers/respuesta.php';
iniciarApi();

try {
    require_once __DIR__ . '/../../config/conexion.php';
} catch (Throwable $error) {
    responderError('Ocurrió un error interno de conexión.', 500);
}

require_once __DIR__ . '/../../app/models/Adeudo.php';
require_once __DIR__ . '/../../app/models/Pago.php';

requerirMetodo('POST');
$idAlumno = obtenerIdAlumnoAutenticado();
$datos = leerJson();
$metodoPago = trim((string) ($datos['metodo_pago'] ?? ''));

if ($metodoPago !== 'Tarjeta') {
    responderError('El método de pago no es válido.', 400);
}

$enTransaccion = false;

try {
    $adeudoModelo = new Adeudo($conn);
    $adeudo = $adeudoModelo->obtenerUltimoPorAlumno($idAlumno);

    if ($adeudo === null) {
        responderError('No se encontró un adeudo para registrar el pago.', 404);
    }

    if ($adeudo['estado'] === 'Pagado') {
        responderError('El adeudo ya se encuentra pagado.', 400);
    }

    $totalPagado = (float) $adeudo['total'];
    $folio = 'UTSC-' . date('YmdHis') . '-' . random_int(100, 999);
    $fechaPago = date('Y-m-d H:i:s');

    $conn->begin_transaction();
    $enTransaccion = true;
    $pagoRegistrado = (new Pago($conn))->registrar(
        $idAlumno,
        $metodoPago,
        $totalPagado,
        $folio,
        $fechaPago,
        (int) $adeudo['id_adeudo']
    );
    $adeudoActualizado = $pagoRegistrado
        && $adeudoModelo->marcarComoPagado((int) $adeudo['id_adeudo']);

    if (!$adeudoActualizado) {
        throw new RuntimeException('No se pudo completar el pago.');
    }

    $conn->commit();
    $enTransaccion = false;

    responderJson([
        'exito' => true,
        'pago' => [
            'id_adeudo' => (int) $adeudo['id_adeudo'],
            'metodo_pago' => $metodoPago,
            'total_pagado' => $totalPagado,
            'folio' => $folio,
            'fecha_pago' => $fechaPago,
        ],
    ], 201);
} catch (Throwable $error) {
    if ($enTransaccion) {
        $conn->rollback();
    }

    responderError('Ocurrió un error interno al registrar el pago.', 500);
}
