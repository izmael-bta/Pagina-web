<?php // Vista: panel principal del administrador. ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css">
</head>
<body class="admin-page">
    <?php require __DIR__ . '/partials/navbar.php'; ?>
    <div class="admin-layout">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>
        <div class="admin-sidebar-overlay" id="admin-sidebar-overlay" hidden></div>
        <main class="admin-main">
            <header class="admin-dashboard-header">
                <div>
                    <h1>Panel de Administración</h1>
                    <p>Bienvenido(a), <?= htmlspecialchars($nombreAdministrador, ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </header>

            <?php if (isset($mensajeAdmin)): ?>
                <p class="admin-alert" role="alert"><?= htmlspecialchars($mensajeAdmin, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <section aria-labelledby="admin-stats-title">
                <h2 class="admin-section-title" id="admin-stats-title">Indicadores generales</h2>
                <div class="admin-stats-grid">
                    <article class="admin-stat-card admin-stat-students">
                        <span class="admin-stat-icon" aria-hidden="true">&#9675;</span>
                        <div><h3>Alumnos registrados</h3><p class="admin-stat-value"><?= number_format((int) $indicadores['alumnos']) ?></p></div>
                    </article>
                    <article class="admin-stat-card admin-stat-debts">
                        <span class="admin-stat-icon" aria-hidden="true">&#9633;</span>
                        <div><h3>Adeudos pendientes</h3><p class="admin-stat-value"><?= number_format((int) $indicadores['adeudos_pendientes']) ?></p></div>
                    </article>
                    <article class="admin-stat-card admin-stat-payments">
                        <span class="admin-stat-icon" aria-hidden="true">$</span>
                        <div><h3>Pagos registrados</h3><p class="admin-stat-value"><?= number_format((int) $indicadores['pagos']) ?></p></div>
                    </article>
                    <article class="admin-stat-card admin-stat-revenue">
                        <span class="admin-stat-icon" aria-hidden="true">$</span>
                        <div><h3>Monto recaudado</h3><p class="admin-stat-value">$<?= number_format((float) $indicadores['monto_recaudado'], 2) ?></p></div>
                    </article>
                </div>
            </section>

            <section class="admin-quick-actions" aria-labelledby="admin-actions-title">
                <h2 class="admin-section-title" id="admin-actions-title">Accesos rápidos</h2>
                <div class="admin-quick-actions-grid">
                    <a class="admin-quick-action" href="<?= BASE_URL ?>/index.php?ruta=admin-alumno-crear">Registrar alumno</a>
                    <a class="admin-quick-action" href="<?= BASE_URL ?>/index.php?ruta=admin-alumnos">Consultar alumnos</a>
                    <a class="admin-quick-action" href="<?= BASE_URL ?>/index.php?ruta=admin-adeudo-crear">Crear adeudo</a>
                    <a class="admin-quick-action" href="<?= BASE_URL ?>/index.php?ruta=admin-pagos">Consultar pagos</a>
                    <a class="admin-quick-action" href="<?= BASE_URL ?>/index.php?ruta=admin-pagos&amp;estado=Pendiente">Validar pagos</a>
                    <a class="admin-quick-action" href="<?= BASE_URL ?>/index.php?ruta=admin-prorroga-crear">Aplicar prórroga</a>
                </div>
            </section>
            <?php require __DIR__ . '/partials/footer.php'; ?>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/admin.js"></script>
</body>
</html>
