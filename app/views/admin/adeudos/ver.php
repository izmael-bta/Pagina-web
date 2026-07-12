<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle del adeudo</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css">
</head>
<body class="admin-page">
    <?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="admin-layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>
        <div class="admin-sidebar-overlay" id="admin-sidebar-overlay" hidden></div>
        <main class="admin-main">
            <section class="admin-debt-detail">
                <h1>Detalle del adeudo</h1>
                <dl>
                    <?php foreach (['Alumno' => $adeudo['nombre'], 'Matrícula' => $adeudo['matricula'], 'Correo' => $adeudo['correo'], 'Periodo' => $periodoTexto] as $etiqueta => $valor): ?>
                        <div><dt><?= $etiqueta ?></dt><dd><?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?></dd></div>
                    <?php endforeach; ?>
                    <?php foreach (['Mensualidad' => 'mensualidad', 'Aportación TSU' => 'aportacion_tsu', 'Atraso' => 'atraso', 'Recargo' => 'recargo', 'Total' => 'total'] as $etiqueta => $campo): ?>
                        <div><dt><?= $etiqueta ?></dt><dd>$<?= number_format((float) $adeudo[$campo], 2) ?></dd></div>
                    <?php endforeach; ?>
                    <div><dt>Estado</dt><dd><span class="admin-debt-status <?= $adeudo['estado'] === 'Pagado' ? 'admin-debt-status-paid' : 'admin-debt-status-pending' ?>"><?= htmlspecialchars($adeudo['estado'], ENT_QUOTES, 'UTF-8') ?></span></dd></div>
                </dl>
                <?php if ($adeudo['estado'] === 'Pagado'): ?>
                    <p class="admin-debt-paid-message">Este adeudo ya fue pagado y no puede modificarse.</p>
                <?php else: ?>
                    <a class="admin-quick-action" href="<?= BASE_URL ?>/index.php?ruta=admin-adeudo-editar&amp;id=<?= (int) $adeudo['id_adeudo'] ?>">Editar</a>
                <?php endif; ?>
                <a class="admin-debt-back-button" href="<?= BASE_URL ?>/index.php?ruta=admin-adeudos">&larr; Volver a Gestión de Adeudos</a>
            </section>
            <?php require __DIR__ . '/../partials/footer.php'; ?>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/admin.js"></script>
</body>
</html>
