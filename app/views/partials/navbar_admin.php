<?php // Vista parcial: navegación privada del administrador. ?>
<nav class="navbar navbar-privada" aria-label="Navegación administrativa">
    <div class="navbar-contenido">
        <a class="navbar-marca" href="<?= BASE_URL ?>/index.php?ruta=admin-dashboard">
            Portal Web de Pagos UTSC
        </a>
        <div class="navbar-alumno-acciones">
            <span class="navbar-alumno-identidad">
                Administrador: <?= htmlspecialchars((string) ($_SESSION['nombre_usuario'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
            </span>
            <a class="boton-cerrar-sesion" href="<?= BASE_URL ?>/index.php?ruta=admin-salir">Cerrar sesión</a>
        </div>
    </div>
</nav>
