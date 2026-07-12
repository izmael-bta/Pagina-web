<?php // Vista parcial: mensaje temporal de un solo uso. ?>
<?php if (!empty($flash['mensaje'])): ?>
    <p class="flash flash-<?= htmlspecialchars($flash['tipo'] ?? 'informacion', ENT_QUOTES, 'UTF-8') ?>" role="status">
        <?= htmlspecialchars($flash['mensaje'], ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>
