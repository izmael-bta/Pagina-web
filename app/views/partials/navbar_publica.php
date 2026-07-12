<?php
// Vista parcial: navegación pública para las pantallas de acceso.
$rutaActiva = $rutaActiva ?? 'login';
?>
<nav class="navbar navbar-publica" aria-label="Navegación principal">
    <div class="navbar-contenido navbar-publica-contenido">
        <a class="navbar-marca" href="<?= BASE_URL ?>/index.php?ruta=login">
            Portal Web de Pagos UTSC
        </a>
        <div class="accesos">
            <button
                class="accesos-boton"
                id="accesos-boton"
                type="button"
                aria-expanded="false"
                aria-controls="accesos-menu"
            >
                <span aria-hidden="true">&#128100;</span>
                <span>Accesos</span>
                <span class="accesos-flecha" aria-hidden="true">&#9662;</span>
            </button>
            <div class="accesos-menu" id="accesos-menu" hidden>
                <a class="<?= $rutaActiva === 'login' ? 'activo' : '' ?>" href="<?= BASE_URL ?>/index.php?ruta=login" <?= $rutaActiva === 'login' ? 'aria-current="page"' : '' ?>>Alumno</a>
                <a class="<?= $rutaActiva === 'login-admin' ? 'activo' : '' ?>" href="<?= BASE_URL ?>/index.php?ruta=login-admin" <?= $rutaActiva === 'login-admin' ? 'aria-current="page"' : '' ?>>Administrador</a>
                <a class="<?= $rutaActiva === 'login-qa' ? 'activo' : '' ?>" href="<?= BASE_URL ?>/index.php?ruta=login-qa" <?= $rutaActiva === 'login-qa' ? 'aria-current="page"' : '' ?>>Control de calidad (QA)</a>
            </div>
        </div>
    </div>
</nav>
