<section class="admin-settings-card"><h2>Nueva configuración</h2>
<form class="admin-settings-form" method="post" action="<?= BASE_URL ?>/index.php?ruta=admin-configuracion-guardar" data-confirm="¿Deseas guardar una nueva versión de la configuración de pagos?">
<input type="hidden" name="csrf_token" value="<?= $esc($csrfToken) ?>">
<div class="admin-settings-field-grid">
<label>Mensualidad<input type="number" name="mensualidad" min="0" max="99999.99" step="0.01" value="<?= $esc(number_format((float)$configuracion['mensualidad'],2,'.','')) ?>" required></label>
<label>Aportación TSU<input type="number" name="aportacion_tsu" min="0" max="99999.99" step="0.01" value="<?= $esc(number_format((float)$configuracion['aportacion_tsu'],2,'.','')) ?>" required></label>
<label>Recargo por vencimiento<input type="number" name="recargo_vencimiento" min="0" max="99999.99" step="0.01" value="<?= $esc(number_format((float)$configuracion['recargo_vencimiento'],2,'.','')) ?>" required></label>
<label>Día límite<input type="number" name="dia_limite" min="1" max="28" step="1" value="<?= (int)$configuracion['dia_limite'] ?>" required></label>
<label>Vigente desde<input type="date" name="vigente_desde" value="<?= date('Y-m-d') ?>" required></label>
</div>
<label class="admin-settings-reason">Motivo del cambio<textarea name="motivo_cambio" rows="4" minlength="10" maxlength="255" required placeholder="Describe el motivo de esta nueva configuración."></textarea></label>
<p class="admin-settings-notice">Esta configuración se aplicará únicamente a los adeudos creados después del cambio. Los registros existentes conservarán sus importes y fechas.</p>
<div class="admin-settings-actions"><a class="admin-settings-action-button admin-settings-cancel-button" href="<?= BASE_URL ?>/index.php?ruta=admin-configuracion">Cancelar</a><button class="admin-settings-action-button admin-settings-save-button" type="submit">Guardar nueva configuración</button></div>
</form></section>
