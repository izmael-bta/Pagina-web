<?php
$esc = static fn(mixed $v): string => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
$fecha = static fn(?string $v): string => $v ? (new DateTimeImmutable($v))->format('d/m/Y') : 'No disponible';
?>
<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Configuración de Pagos</title><link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css"><link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css"></head>
<body class="admin-page"><?php require __DIR__ . '/../partials/navbar.php'; ?><div class="admin-layout"><?php require __DIR__ . '/../partials/sidebar.php'; ?><div class="admin-sidebar-overlay" id="admin-sidebar-overlay" hidden></div>
<main class="admin-main"><?php require __DIR__ . '/../../partials/flash.php'; ?>
<header class="admin-settings-header"><h1>Configuración de Pagos</h1><p>Administra los valores predeterminados para los nuevos adeudos.</p></header>

<?php if (!$configuracion): ?>
<section class="admin-settings-card admin-settings-missing"><h2>Configuración no disponible</h2><p>No existe una configuración de pagos activa.</p></section>
<?php else: ?>
<section class="admin-settings-current"><h2>Configuración vigente</h2><div class="admin-settings-current-grid">
<?php foreach ([['Mensualidad','$'.number_format((float)$configuracion['mensualidad'],2)],['Aportación TSU','$'.number_format((float)$configuracion['aportacion_tsu'],2)],['Recargo por vencimiento','$'.number_format((float)$configuracion['recargo_vencimiento'],2)],['Día límite de pago','Día '.(int)$configuracion['dia_limite'].' de cada mes']] as [$label,$value]): ?><article><span><?= $esc($label) ?></span><strong><?= $esc($value) ?></strong></article><?php endforeach; ?>
</div><dl class="admin-settings-meta"><div><dt>Vigente desde</dt><dd><?= $fecha($configuracion['vigente_desde']) ?></dd></div><div><dt>Configurada por</dt><dd><?= $esc($configuracion['creada_por_nombre'] ?? 'Configuración inicial') ?></dd></div><div><dt>Motivo del último cambio</dt><dd><?= $esc($configuracion['motivo_cambio']) ?></dd></div></dl></section>
<?php require __DIR__ . '/partials/formulario.php'; ?>
<?php endif; ?>

<section class="admin-settings-history"><h2>Historial de configuraciones</h2><div class="admin-settings-table-wrap"><table class="admin-settings-table"><thead><tr><th>Vigente desde</th><th>Mensualidad</th><th>Aportación TSU</th><th>Recargo</th><th>Día límite</th><th>Motivo</th><th>Configurada por</th><th>Fecha de registro</th><th>Estado</th></tr></thead><tbody>
<?php if (!$historial): ?><tr><td colspan="9" class="admin-settings-empty">No hay configuraciones registradas.</td></tr><?php else: foreach ($historial as $item): ?><tr><td><?= $fecha($item['vigente_desde']) ?></td><td>$<?= number_format((float)$item['mensualidad'],2) ?></td><td>$<?= number_format((float)$item['aportacion_tsu'],2) ?></td><td>$<?= number_format((float)$item['recargo_vencimiento'],2) ?></td><td>Día <?= (int)$item['dia_limite'] ?></td><td><?= $esc($item['motivo_cambio']) ?></td><td><?= $esc($item['creada_por_nombre'] ?? 'Configuración inicial') ?></td><td><?= $esc((new DateTimeImmutable($item['fecha_creacion']))->format('d/m/Y H:i')) ?></td><td><span class="admin-settings-status <?= (int)$item['activa']===1?'admin-settings-status-active':'admin-settings-status-history' ?>"><?= (int)$item['activa']===1?'Activa':'Histórica' ?></span></td></tr><?php endforeach; endif; ?>
</tbody></table></div><nav class="admin-settings-pagination" aria-label="Paginación"><?php if($pagina>1):?><a href="<?=BASE_URL?>/index.php?ruta=admin-configuracion&amp;pagina=<?=$pagina-1?>">Anterior</a><?php endif;?><span>Página <?=$pagina?> de <?=$paginas?></span><?php if($pagina<$paginas):?><a href="<?=BASE_URL?>/index.php?ruta=admin-configuracion&amp;pagina=<?=$pagina+1?>">Siguiente</a><?php endif;?></nav></section>
<?php require __DIR__ . '/../partials/footer.php'; ?></main></div><script src="<?= BASE_URL ?>/public/js/admin.js"></script></body></html>
