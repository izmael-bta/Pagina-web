<?php // Vista: comprobante del pago registrado. ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Pago</title>
    <link rel="stylesheet" href="/Portal_Web_Pagos_UTSC/public/css/estilos.css">
    <link rel="stylesheet" href="/Portal_Web_Pagos_UTSC/public/css/comprobante.css">
</head>
<body class="receipt-page">
    <?php require_once __DIR__ . '/../partials/navbar_alumno.php'; ?>
    <main id="receipt-screen" class="receipt-screen">
        <header class="receipt-header">
            <h1>Comprobante de Pago</h1>
            <p class="receipt-subtitle">Tu pago fue registrado correctamente</p>
        </header>

        <div class="receipt-layout">
            <section class="receipt-card receipt-detail-card" aria-labelledby="receipt-detail-title">
                <h2 id="receipt-detail-title"><span aria-hidden="true">&#128196;</span> Detalle del comprobante</h2>
                <dl class="receipt-details">
                    <div class="receipt-detail-row">
                        <dt class="receipt-label">Nombre del alumno</dt>
                        <dd class="receipt-value"><?= htmlspecialchars($alumno['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="receipt-detail-row">
                        <dt class="receipt-label">Matrícula</dt>
                        <dd class="receipt-value"><?= htmlspecialchars($alumno['matricula'] ?? '', ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="receipt-detail-row">
                        <dt class="receipt-label">Método de pago</dt>
                        <dd class="receipt-value"><?= htmlspecialchars($metodoPago, ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="receipt-detail-row">
                        <dt class="receipt-label">Periodo pagado</dt>
                        <dd class="receipt-value"><?= htmlspecialchars($periodoPagado, ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="receipt-detail-row receipt-total-row">
                        <dt class="receipt-label">Total pagado</dt>
                        <dd class="receipt-value">$<?= number_format((float) $totalPagado, 2) ?></dd>
                    </div>
                    <div class="receipt-detail-row">
                        <dt class="receipt-label">Folio</dt>
                        <dd class="receipt-value"><?= htmlspecialchars($folio, ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="receipt-detail-row">
                        <dt class="receipt-label">Fecha de pago</dt>
                        <dd class="receipt-value"><?= htmlspecialchars($fechaPago, ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                </dl>
            </section>

            <section class="receipt-card receipt-status-card" aria-labelledby="receipt-status-title">
                <div class="receipt-success-icon" aria-hidden="true">&#10003;</div>
                <h2 id="receipt-status-title">Tu pago fue registrado correctamente</h2>
                <div class="receipt-divider" aria-hidden="true"></div>
                <div class="receipt-mail-box">
                    <p>Tu recibo será enviado a tu correo:</p>
                    <strong><?= htmlspecialchars($alumno['correo'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <a class="receipt-main-button no-imprimir" href="<?= BASE_URL ?>/index.php?ruta=alumno">Volver al menú principal</a>
            </section>
        </div>
    </main>
</body>
</html>
