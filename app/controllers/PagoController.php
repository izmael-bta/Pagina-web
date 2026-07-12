<?php
require_once __DIR__ . '/../models/Alumno.php';
require_once __DIR__ . '/../models/Adeudo.php';
require_once __DIR__ . '/../models/Pago.php';
require_once __DIR__ . '/../models/Prorroga.php';
require_once __DIR__ . '/../helpers/PeriodoHelper.php';

// Controlador: validación, registro del pago y comprobante.
class PagoController
{
    private mysqli $conexion;
    private Alumno $alumnoModelo;
    private Adeudo $adeudoModelo;
    private Pago $pagoModelo;
    private Prorroga $prorrogaModelo;

    public function __construct(mysqli $conexion)
    {
        $this->conexion = $conexion;
        $this->alumnoModelo = new Alumno($conexion);
        $this->adeudoModelo = new Adeudo($conexion);
        $this->pagoModelo = new Pago($conexion);
        $this->prorrogaModelo = new Prorroga($conexion);
    }

    public function formulario(): void
    {
        $idAlumno = $this->obtenerIdAlumno();
        $message = '';
        $nombreTitular = '';
        $alumno = $this->alumnoModelo->buscarPorId($idAlumno);
        $this->adeudoModelo->actualizarRecargosVencidos();
        $adeudo = $this->adeudoModelo->obtenerUltimoPorAlumno($idAlumno);

        if ($alumno === null || $adeudo === null) {
            header('Location: ' . BASE_URL . '/index.php?ruta=alumno');
            exit;
        }

        $periodoFormateado = PeriodoHelper::formatear($adeudo['periodo'] ?? null);
        $ultimaProrroga = $this->prorrogaModelo->obtenerUltimaPorAdeudo((int) $adeudo['id_adeudo']);
        $fechaLimite = !empty($adeudo['fecha_limite']) ? DateTimeImmutable::createFromFormat('!Y-m-d', $adeudo['fecha_limite']) : false;
        $fechaLimiteTexto = $fechaLimite ? $fechaLimite->format('d/m/Y') : 'No especificada';
        $prorrogaVigente = $ultimaProrroga !== null
            && $adeudo['estado'] === 'Pendiente'
            && $fechaLimite
            && new DateTimeImmutable('today') <= $fechaLimite;
        $anioActual = (int) date('Y');
        $aniosVencimiento = range($anioActual, $anioActual + 10);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $metodoPago = is_string($_POST['metodo_pago'] ?? null)
                ? trim($_POST['metodo_pago'])
                : '';
            $nombreTitular = is_string($_POST['nombre_titular'] ?? null)
                ? trim($_POST['nombre_titular'])
                : '';
            $numeroTarjetaOriginal = is_string($_POST['numero_tarjeta'] ?? null)
                ? $_POST['numero_tarjeta']
                : '';
            $mesVencimiento = is_string($_POST['mes_vencimiento'] ?? null)
                ? $_POST['mes_vencimiento']
                : '';
            $anioVencimiento = is_string($_POST['anio_vencimiento'] ?? null)
                ? $_POST['anio_vencimiento']
                : '';
            $cvvOriginal = is_string($_POST['cvv'] ?? null)
                ? $_POST['cvv']
                : '';
            $numeroTarjeta = preg_replace('/\D/', '', $numeroTarjetaOriginal);
            $errores = [];

            if ($metodoPago !== 'Tarjeta') {
                $errores[] = 'El método de pago debe ser Tarjeta.';
            }

            $longitudNombre = function_exists('mb_strlen')
                ? mb_strlen($nombreTitular, 'UTF-8')
                : strlen($nombreTitular);

            if ($nombreTitular === '' || $longitudNombre > 100) {
                $errores[] = 'Ingresa un nombre válido para el titular.';
            }

            $numeroTarjetaValido = preg_match('/^\d{16}$/', $numeroTarjeta) === 1;

            if (!$numeroTarjetaValido) {
                $errores[] = 'El número de tarjeta debe contener exactamente 16 dígitos.';
            }

            $mes = filter_var($mesVencimiento, FILTER_VALIDATE_INT);
            $anio = filter_var($anioVencimiento, FILTER_VALIDATE_INT);
            $mesActual = (int) date('n');

            if ($mes === false || $anio === false
                || $mes < 1 || $mes > 12
                || $anio < $anioActual || $anio > $anioActual + 10
                || ($anio === $anioActual && $mes < $mesActual)) {
                $errores[] = 'La fecha de vencimiento no es válida.';
            }

            if (!preg_match('/^[0-9]{3,4}$/', $cvvOriginal)) {
                $errores[] = 'El CVV debe contener 3 o 4 dígitos.';
            }

            if ($adeudo['estado'] === 'Pagado') {
                $errores[] = 'Este adeudo ya fue pagado.';
            }

            if ((float) $adeudo['total'] <= 0) {
                $errores[] = 'El importe del adeudo no es válido.';
            }

            // Los datos sensibles dejan de utilizarse antes de cualquier escritura o sesión.
            $numeroTarjetaOriginal = '';
            $numeroTarjeta = '';
            $cvvOriginal = '';
            $mesVencimiento = '';
            $anioVencimiento = '';

            if ($errores !== []) {
                $message = implode(' ', $errores);
            } else {
                $enTransaccion = false;

                try {
                    $this->conexion->begin_transaction();
                    $enTransaccion = true;
                    $this->adeudoModelo->actualizarRecargoDeAdeudo((int) $adeudo['id_adeudo']);
                    $adeudoBloqueado = $this->adeudoModelo->obtenerUltimoPorAlumno($idAlumno, true);

                    if ($adeudoBloqueado === null
                        || (int) $adeudoBloqueado['id_alumno'] !== $idAlumno
                        || $adeudoBloqueado['estado'] === 'Pagado'
                        || (float) $adeudoBloqueado['total'] <= 0) {
                        throw new RuntimeException('El adeudo no está disponible para pago.');
                    }

                    $totalPagado = (float) $adeudoBloqueado['total'];
                    $folio = 'UTSC-' . date('YmdHis') . '-' . random_int(100, 999);
                    $fechaPago = date('Y-m-d H:i:s');
                    $pagoRegistrado = $this->pagoModelo->registrar(
                        $idAlumno,
                        'Tarjeta',
                        $totalPagado,
                        $folio,
                        $fechaPago,
                        (int) $adeudoBloqueado['id_adeudo']
                    );
                    $adeudoActualizado = $pagoRegistrado
                        && $this->adeudoModelo->marcarComoPagado((int) $adeudoBloqueado['id_adeudo']);

                    if (!$adeudoActualizado) {
                        throw new RuntimeException('No se pudo completar el pago.');
                    }

                    $this->conexion->commit();
                    $enTransaccion = false;
                    $_SESSION['metodo_pago'] = 'Tarjeta';
                    $_SESSION['total_pagado'] = $totalPagado;
                    $_SESSION['folio'] = $folio;
                    $_SESSION['fecha_pago'] = $fechaPago;
                    $_SESSION['id_adeudo_pagado'] = (int) $adeudoBloqueado['id_adeudo'];

                    header('Location: ' . BASE_URL . '/index.php?ruta=comprobante');
                    exit;
                } catch (Throwable $error) {
                    if ($enTransaccion) {
                        $this->conexion->rollback();
                    }

                    $message = 'No se pudo registrar el pago. Verifica que el adeudo continúe pendiente.';
                }
            }
        }

        require __DIR__ . '/../views/pago/formulario.php';
    }

    public function comprobante(): void
    {
        $idAlumno = $this->obtenerIdAlumno();
        $alumno = $this->alumnoModelo->buscarPorId($idAlumno);
        $metodoPago = $_SESSION['metodo_pago'] ?? '';
        $totalPagado = $_SESSION['total_pagado'] ?? 0;
        $folio = $_SESSION['folio'] ?? '';
        $fechaPago = $_SESSION['fecha_pago'] ?? '';
        $idAdeudoPagado = isset($_SESSION['id_adeudo_pagado'])
            ? (int) $_SESSION['id_adeudo_pagado']
            : null;
        $pagoComprobante = is_string($folio) && $folio !== ''
            ? $this->pagoModelo->buscarPorFolioYAlumno($idAlumno, $folio, $idAdeudoPagado)
            : null;
        $periodoPagado = PeriodoHelper::formatear($pagoComprobante['periodo'] ?? null);

        require __DIR__ . '/../views/pago/comprobante.php';
    }

    private function obtenerIdAlumno(): int
    {
        if (!isset($_SESSION['id_alumno']) || ($_SESSION['rol'] ?? '') !== 'alumno') {
            header('Location: ' . BASE_URL . '/index.php?ruta=login');
            exit;
        }

        return (int) $_SESSION['id_alumno'];
    }
}
