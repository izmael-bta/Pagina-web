<?php
$idsElegibles = array_map(static fn(array $adeudo): int => (int) $adeudo['id_adeudo'], $adeudos);
$idInicial = in_array($preseleccion, $idsElegibles, true) ? $preseleccion : 0;
$adeudosBuscador = array_map(static fn(array $adeudo): array => [
    'id_adeudo' => (int) $adeudo['id_adeudo'],
    'matricula' => (string) $adeudo['matricula'],
    'alumno' => (string) $adeudo['nombre'],
    'periodo' => PeriodoHelper::formatear($adeudo['periodo']),
    'total' => number_format((float) $adeudo['total'], 2, '.', ''),
    'estado' => 'Pendiente',
    'fecha_limite' => (string) $adeudo['fecha_limite'],
], $adeudos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplicar prórroga</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css">
</head>
<body class="admin-page">
    <?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="admin-layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>
        <div class="admin-sidebar-overlay" id="admin-sidebar-overlay" hidden></div>
        <main class="admin-main">
            <header class="admin-extension-form-header">
                <h1>Aplicar prórroga</h1>
                <p>Extiende la fecha límite de un adeudo pendiente.</p>
            </header>

            <form class="admin-extension-form-card" method="post" action="<?= BASE_URL ?>/index.php?ruta=admin-prorroga-guardar" data-confirm="¿Deseas aplicar esta prórroga? La fecha límite del adeudo será actualizada.">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                <div class="admin-extension-form-field admin-extension-search">
                    <label for="admin-extension-debt-search">Buscar adeudo pendiente</label>
                    <input type="search" id="admin-extension-debt-search" class="admin-extension-search-input" placeholder="Escribe la matrícula del alumno" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="admin-extension-results" aria-autocomplete="list">
                    <div class="admin-extension-results" id="admin-extension-results" role="listbox" hidden></div>
                    <input type="hidden" name="id_adeudo" id="id_adeudo" value="<?= $idInicial > 0 ? $idInicial : '' ?>">
                    <script type="application/json" id="admin-extension-debts-data"><?= json_encode($adeudosBuscador, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
                </div>

                <section class="admin-extension-summary" aria-labelledby="admin-extension-summary-title">
                    <h2 id="admin-extension-summary-title">Información del adeudo</h2>
                    <p class="admin-extension-summary-empty" id="admin-extension-summary-empty" <?= $idInicial > 0 ? 'hidden' : '' ?>>Selecciona un adeudo pendiente para consultar su información.</p>
                    <dl class="admin-extension-summary-grid" id="admin-extension-summary-grid" <?= $idInicial === 0 ? 'hidden' : '' ?>>
                        <div><dt>Alumno</dt><dd id="admin-extension-student">—</dd></div>
                        <div><dt>Matrícula</dt><dd id="admin-extension-enrollment">—</dd></div>
                        <div><dt>Periodo</dt><dd id="admin-extension-period">—</dd></div>
                        <div><dt>Total</dt><dd id="admin-extension-total">—</dd></div>
                        <div><dt>Estado</dt><dd id="admin-extension-status">—</dd></div>
                        <div><dt>Fecha límite actual</dt><dd id="admin-extension-limit-summary">—</dd></div>
                    </dl>
                </section>

                <div class="admin-extension-date-grid">
                    <label class="admin-extension-form-field">Fecha límite actual
                        <input id="admin-extension-current" type="text" readonly>
                    </label>
                    <label class="admin-extension-form-field">Nueva fecha límite
                        <input id="admin-extension-new" type="date" name="nueva_fecha" required <?= $idInicial === 0 ? 'disabled' : '' ?>>
                    </label>
                </div>

                <label class="admin-extension-form-field admin-extension-reason">Motivo de la prórroga
                    <textarea id="admin-extension-reason" name="motivo" rows="4" minlength="5" maxlength="255" required placeholder="Describe el motivo por el cual se autoriza la prórroga." <?= $idInicial === 0 ? 'disabled' : '' ?>></textarea>
                </label>

                <p class="admin-extension-notice">Esta acción extenderá la fecha límite del adeudo. Los importes, el periodo y el estado no serán modificados.</p>

                <div class="admin-extension-form-actions">
                    <a class="admin-extension-action-button admin-extension-cancel-button" href="<?= BASE_URL ?>/index.php?ruta=admin-prorrogas">Cancelar</a>
                    <button class="admin-extension-action-button admin-extension-submit-button" id="admin-extension-submit" type="submit" <?= $idInicial === 0 ? 'disabled' : '' ?>>Aplicar prórroga</button>
                </div>
            </form>
            <?php require __DIR__ . '/../partials/footer.php'; ?>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/admin.js"></script>
</body>
</html>
