<?php // Vista: marcador reutilizable para módulos administrativos futuros. ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloModulo, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css">
</head>
<body class="admin-page">
    <?php require __DIR__ . '/partials/navbar.php'; ?>
    <div class="admin-layout">
        <?php require __DIR__ . '/partials/sidebar.php'; ?>
        <div class="admin-sidebar-overlay" id="admin-sidebar-overlay" hidden></div>
        <main class="admin-main">
            <section class="admin-module-placeholder">
                <h1><?= htmlspecialchars($tituloModulo, ENT_QUOTES, 'UTF-8') ?></h1>
                <p>Módulo en construcción</p>
                <a class="admin-quick-action" href="<?= BASE_URL ?>/index.php?ruta=admin-dashboard">Volver al panel principal</a>
            </section>
            <?php require __DIR__ . '/partials/footer.php'; ?>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/admin.js"></script>
</body>
</html>
