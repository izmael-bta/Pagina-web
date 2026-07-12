<?php
$adminCssPath = __DIR__ . '/../../../../public/css/admin.css';
$adminJsPath = __DIR__ . '/../../../../public/js/admin.js';
$adminCssVersion = file_exists($adminCssPath) ? (string) filemtime($adminCssPath) : (string) time();
$adminJsVersion = file_exists($adminJsPath) ? (string) filemtime($adminJsPath) : (string) time();
$esc = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$fechaLegible = static function (?string $value): string {
    if ($value === null || trim($value) === '') return 'Pendiente';
    try {
        $fecha = new DateTimeImmutable($value);
        $hora = (int) $fecha->format('G');
        return $fecha->format('d/m/Y ') . $fecha->format('g:i') . ($hora < 12 ? ' a. m.' : ' p. m.');
    } catch (Throwable) {
        return 'Pendiente';
    }
};
$estado = (string) $aclaracion['estado'];
$estadoClase = match ($estado) {
    'Abierta' => 'abierta',
    'En revisión' => 'en-revision',
    'Resuelta' => 'resuelta',
    'Rechazada' => 'rechazada',
    default => 'abierta',
};
$adeudoRelacionado = !empty($aclaracion['periodo'])
    ? PeriodoHelper::formatear($aclaracion['periodo']) . ' · Adeudo relacionado'
    : 'Sin adeudo relacionado';
$pagoRelacionado = !empty($aclaracion['folio_pago'])
    ? (string) $aclaracion['folio_pago'] . ' · Pago relacionado'
    : 'Sin pago relacionado';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalle de aclaración</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css?v=<?= rawurlencode($adminCssVersion) ?>">
</head>
<body class="admin-page">
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<div class="admin-layout">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>
    <div class="admin-sidebar-overlay" id="admin-sidebar-overlay" hidden></div>
    <main class="admin-main">
        <?php require __DIR__ . '/../../partials/flash.php'; ?>

        <header class="admin-clarification-detail-header">
            <div><h1>Detalle de aclaración</h1><p>Folio <?= $esc($aclaracion['folio']) ?></p></div>
            <span class="admin-clarification-status admin-clarification-status-<?= $estadoClase ?>"><?= $esc($estado) ?></span>
        </header>

        <div class="admin-clarification-detail-grid">
            <section class="admin-clarification-detail-card">
                <h2>Información de la aclaración</h2>
                <?php foreach ([
                    'Folio' => $aclaracion['folio'],
                    'Alumno' => $aclaracion['nombre'],
                    'Matrícula' => $aclaracion['matricula'],
                    'Correo' => $aclaracion['correo'],
                    'Tipo' => $aclaracion['tipo'],
                    'Asunto' => $aclaracion['asunto'],
                    'Adeudo relacionado' => $adeudoRelacionado,
                    'Pago relacionado' => $pagoRelacionado,
                ] as $label => $value): ?>
                    <div class="admin-clarification-detail-row"><span class="admin-clarification-detail-label"><?= $esc($label) ?></span><span class="admin-clarification-detail-value"><?= $esc($value) ?></span></div>
                <?php endforeach; ?>
                <div class="admin-clarification-description-box">
                    <h3>Descripción del caso</h3>
                    <p><?= nl2br($esc($aclaracion['descripcion'])) ?></p>
                </div>
            </section>

            <aside class="admin-clarification-tracking-card">
                <h2>Seguimiento</h2>
                <?php foreach ([
                    'Estado' => $estado,
                    'Registrada por' => $aclaracion['registrada_por_nombre'] ?? 'No disponible',
                    'Fecha de registro' => $fechaLegible($aclaracion['fecha_registro'] ?? null),
                    'Atendida por' => $aclaracion['atendida_por_nombre'] ?? 'Pendiente',
                    'Fecha de atención' => $fechaLegible($aclaracion['fecha_atencion'] ?? null),
                    'Respuesta administrativa' => $aclaracion['respuesta'] ?? 'Sin respuesta',
                ] as $label => $value): ?>
                    <div class="admin-clarification-detail-row"><span class="admin-clarification-detail-label"><?= $esc($label) ?></span><span class="admin-clarification-detail-value"><?= $esc($value) ?></span></div>
                <?php endforeach; ?>

                <?php if ($estado === 'Abierta'): ?>
                    <form class="admin-clarification-review-form" method="post" action="<?= BASE_URL ?>/index.php?ruta=admin-aclaracion-revision" data-confirm="¿Deseas marcar esta aclaración como En revisión?">
                        <input type="hidden" name="csrf_token" value="<?= $esc($csrfToken) ?>">
                        <input type="hidden" name="id_aclaracion" value="<?= (int) $aclaracion['id_aclaracion'] ?>">
                        <button class="admin-clarification-review-button" type="submit">Marcar en revisión</button>
                    </form>
                <?php endif; ?>

                <?php if (in_array($estado, ['Abierta', 'En revisión'], true)): ?>
                    <section class="admin-clarification-response-section">
                        <h3>Responder aclaración</h3>
                        <form method="post" class="admin-clarification-response-form">
                            <input type="hidden" name="csrf_token" value="<?= $esc($csrfToken) ?>">
                            <input type="hidden" name="id_aclaracion" value="<?= (int) $aclaracion['id_aclaracion'] ?>">
                            <label for="respuesta">Respuesta administrativa</label>
                            <textarea id="respuesta" name="respuesta" rows="5" required minlength="10" maxlength="1000" placeholder="Escribe la respuesta que recibirá esta aclaración."></textarea>
                            <div class="admin-clarification-response-actions">
                                <button type="submit" formaction="<?= BASE_URL ?>/index.php?ruta=admin-aclaracion-rechazar" class="admin-clarification-reject-button" data-confirm-submit="¿Deseas rechazar esta aclaración?">Rechazar</button>
                                <button type="submit" formaction="<?= BASE_URL ?>/index.php?ruta=admin-aclaracion-resolver" class="admin-clarification-resolve-button" data-confirm-submit="¿Deseas marcar esta aclaración como Resuelta?">Resolver</button>
                            </div>
                        </form>
                    </section>
                <?php elseif ($estado === 'Resuelta'): ?>
                    <p class="admin-clarification-final-message admin-clarification-final-resolved">Esta aclaración fue resuelta.</p>
                <?php elseif ($estado === 'Rechazada'): ?>
                    <p class="admin-clarification-final-message admin-clarification-final-rejected">Esta aclaración fue rechazada.</p>
                <?php endif; ?>
            </aside>
        </div>

        <a class="admin-clarification-back-button" href="<?= BASE_URL ?>/index.php?ruta=admin-aclaraciones">← Volver a Gestión de Aclaraciones</a>
        <?php require __DIR__ . '/../partials/footer.php'; ?>
    </main>
</div>
<script src="<?= BASE_URL ?>/public/js/admin.js?v=<?= rawurlencode($adminJsVersion) ?>" defer></script>
</body>
</html>
