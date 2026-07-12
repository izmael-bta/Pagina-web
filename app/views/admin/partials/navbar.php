<?php // Vista parcial: barra superior administrativa reutilizable. ?>
<header class="admin-navbar">
    <div class="admin-navbar-content">
        <button class="admin-menu-toggle" id="admin-menu-toggle" type="button" aria-expanded="false" aria-controls="admin-sidebar" aria-label="Abrir menú administrativo">
            <span aria-hidden="true">&#9776;</span>
        </button>
        <a class="admin-navbar-brand" href="<?= BASE_URL ?>/index.php?ruta=admin-dashboard">Portal Web de Pagos UTSC</a>
        <div class="admin-navbar-user">
            <span>Administrador: <?= htmlspecialchars((string) ($_SESSION['nombre_usuario'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
            <a href="<?= BASE_URL ?>/index.php?ruta=admin-salir">Cerrar sesión</a>
        </div>
    </div>
</header>
