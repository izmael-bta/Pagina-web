<?php // Vista: dashboard privado del alumno. ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Datos del Alumno</title>
    <link rel="stylesheet" href="/Portal_Web_Pagos_UTSC/public/css/estilos.css">
    <link rel="stylesheet" href="/Portal_Web_Pagos_UTSC/public/css/dashboard.css">
</head>
<body class="student-dashboard-page">
    <?php require_once __DIR__ . '/../partials/navbar_alumno.php'; ?>
    <main id="student-dashboard" class="student-dashboard-main">
        <header class="student-dashboard-header">
            <img src="/Portal_Web_Pagos_UTSC/public/img/logo.png" alt="Universidad Tecnológica Santa Catarina" class="student-dashboard-logo">
            <h1 class="student-dashboard-title">Datos del Alumno</h1>
            <p class="student-dashboard-subtitle">Consulta de información y adeudos escolares</p>
            <div class="student-dashboard-divider" aria-hidden="true"></div>
        </header>

        <div class="student-dashboard-grid">
            <section class="student-dashboard-card" aria-labelledby="student-info-title">
                <h2 class="student-dashboard-card-header" id="student-info-title"><span aria-hidden="true">&#128100;</span> Información del Alumno</h2>
                <dl class="student-dashboard-rows">
                    <div class="student-dashboard-row">
                        <dt class="student-dashboard-label"><span aria-hidden="true">&#127380;</span> Matrícula</dt>
                        <dd class="student-dashboard-value"><?= htmlspecialchars($alumno['matricula'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="student-dashboard-row">
                        <dt class="student-dashboard-label"><span aria-hidden="true">&#128100;</span> Nombre</dt>
                        <dd class="student-dashboard-value"><?= htmlspecialchars($alumno['nombre'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="student-dashboard-row">
                        <dt class="student-dashboard-label"><span aria-hidden="true">&#9993;</span> Correo</dt>
                        <dd class="student-dashboard-value"><?= htmlspecialchars($alumno['correo'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="student-dashboard-row">
                        <dt class="student-dashboard-label"><span aria-hidden="true">&#127891;</span> Carrera</dt>
                        <dd class="student-dashboard-value"><?= htmlspecialchars($alumno['carrera'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="student-dashboard-row">
                        <dt class="student-dashboard-label"><span aria-hidden="true">&#128101;</span> Grupo</dt>
                        <dd class="student-dashboard-value"><?= htmlspecialchars($alumno['grupo'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                </dl>
            </section>

            <section class="student-dashboard-card" aria-labelledby="student-debt-title">
                <h2 class="student-dashboard-card-header" id="student-debt-title"><span aria-hidden="true">&#128203;</span> Resumen de Adeudos</h2>
                <?php if ($adeudo): ?>
                    <dl class="student-dashboard-rows">
                        <div class="student-dashboard-row">
                            <dt class="student-dashboard-label">Periodo</dt>
                            <dd class="student-dashboard-value"><?= htmlspecialchars($periodoFormateado, ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <div class="student-dashboard-row">
                            <dt class="student-dashboard-label"><?= $tieneProrroga ? 'Fecha límite actual' : 'Fecha límite' ?></dt>
                            <dd class="student-dashboard-value"><?= htmlspecialchars($fechaLimiteTexto, ENT_QUOTES, 'UTF-8') ?></dd>
                        </div>
                        <?php if ($tieneProrroga && !$adeudoPagado): ?>
                            <div class="student-dashboard-row">
                                <dt class="student-dashboard-label">Prórroga</dt>
                                <dd class="student-dashboard-value"><span class="student-dashboard-extension <?= $estadoProrroga === 'Vigente' ? 'student-dashboard-extension-active' : 'student-dashboard-extension-expired' ?>"><?= htmlspecialchars($estadoProrroga, ENT_QUOTES, 'UTF-8') ?></span></dd>
                            </div>
                        <?php endif; ?>
                        <div class="student-dashboard-row">
                            <dt class="student-dashboard-label">Mensualidad</dt>
                            <dd class="student-dashboard-value">$<?= number_format((float) $adeudo['mensualidad'], 2) ?></dd>
                        </div>
                        <div class="student-dashboard-row">
                            <dt class="student-dashboard-label">Aportación TSU</dt>
                            <dd class="student-dashboard-value">$<?= number_format((float) $adeudo['aportacion_tsu'], 2) ?></dd>
                        </div>
                        <div class="student-dashboard-row">
                            <dt class="student-dashboard-label">Atraso</dt>
                            <dd class="student-dashboard-value">$<?= number_format((float) ($adeudo['atraso'] ?? 0), 2) ?></dd>
                        </div>
                        <div class="student-dashboard-row">
                            <dt class="student-dashboard-label">Recargo por atraso</dt>
                            <dd class="student-dashboard-value">$<?= number_format((float) $adeudo['recargo'], 2) ?></dd>
                        </div>
                        <?php if (!$adeudoPagado): ?>
                            <div class="student-dashboard-row student-dashboard-total">
                                <dt class="student-dashboard-label">Total a pagar</dt>
                                <dd class="student-dashboard-value">$<?= number_format((float) $totalAdeudo, 2) ?></dd>
                            </div>
                        <?php endif; ?>
                        <div class="student-dashboard-row">
                            <dt class="student-dashboard-label">Estado</dt>
                            <dd class="student-dashboard-value">
                                <span class="student-dashboard-status <?= $estadoAdeudo === 'Pagado' ? 'student-dashboard-status-paid' : 'student-dashboard-status-pending' ?>">
                                    <?= $estadoAdeudo === 'Pagado' ? '<span aria-hidden="true">&#10003;</span> ' : '' ?>
                                    <?= htmlspecialchars($estadoAdeudo, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </dd>
                        </div>
                    </dl>
                <?php else: ?>
                    <p class="student-dashboard-empty">No se encontraron adeudos para este alumno.</p>
                <?php endif; ?>
            </section>

            <section class="student-dashboard-card student-dashboard-actions" aria-labelledby="student-actions-title">
                <h2 class="student-dashboard-card-header" id="student-actions-title"><span aria-hidden="true">&#9881;</span> Acciones</h2>
                <?php if ($adeudo && $estadoAdeudo === 'Pagado'): ?>
                    <div class="student-dashboard-success" aria-hidden="true">&#10003;</div>
                    <h3>¡Este adeudo ya fue pagado!</h3>
                    <p>Gracias por mantener tus pagos al día.</p>
                    <?php if ($tieneProrroga): ?><p class="student-dashboard-extension-message"><?= htmlspecialchars($mensajeProrroga, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                    <?php if ($existeComprobante): ?>
                        <a class="student-dashboard-primary-action" href="<?= BASE_URL ?>/index.php?ruta=comprobante">Ver comprobante de pago</a>
                    <?php endif; ?>
                <?php elseif ($adeudo): ?>
                    <div class="student-dashboard-pending-icon" aria-hidden="true">&#9203;</div>
                    <h3>Tienes un adeudo pendiente.</h3>
                    <p>Realiza tu pago para mantener tu cuenta al corriente.</p>
                    <?php if ($avisoRecargo): ?><p class="student-dashboard-surcharge-notice">Se aplicó un recargo de $<?= number_format((float) $adeudo['recargo'], 2) ?> por vencimiento de la fecha límite.</p><?php endif; ?>
                    <?php if ($tieneProrroga): ?><p class="student-dashboard-extension-message <?= $estadoProrroga === 'Vencida' ? 'student-dashboard-extension-message-expired' : '' ?>"><?= htmlspecialchars($mensajeProrroga, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                    <a class="student-dashboard-primary-action" href="<?= BASE_URL ?>/index.php?ruta=pago">Realizar pago</a>
                <?php else: ?>
                    <div class="student-dashboard-info-icon" aria-hidden="true">i</div>
                    <h3>No tienes adeudos registrados.</h3>
                    <p>Actualmente tu cuenta se encuentra al corriente.</p>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
