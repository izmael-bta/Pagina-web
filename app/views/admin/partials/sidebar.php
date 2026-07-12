<?php
// Vista parcial: menú reutilizable de los módulos administrativos.
$opcionesAdmin = [
    ['ruta' => 'admin-dashboard', 'icono' => '&#9632;', 'texto' => 'Inicio'],
    ['ruta' => 'admin-alumnos', 'icono' => '&#9675;', 'texto' => 'Alumnos'],
    ['ruta' => 'admin-adeudos', 'icono' => '&#9633;', 'texto' => 'Adeudos'],
    ['ruta' => 'admin-pagos', 'icono' => '$', 'texto' => 'Pagos'],
    ['ruta' => 'admin-prorrogas', 'icono' => '&#9201;', 'texto' => 'Prórrogas'],
    ['ruta' => 'admin-aclaraciones', 'icono' => '?', 'texto' => 'Aclaraciones'],
    ['ruta' => 'admin-configuracion', 'icono' => '&#9881;', 'texto' => 'Configuración'],
    ['ruta' => 'admin-reportes', 'icono' => '&#9776;', 'texto' => 'Reportes'],
    ['ruta' => 'admin-estadisticas', 'icono' => '&#9652;', 'texto' => 'Estadísticas'],
];
?>
<aside class="admin-sidebar" id="admin-sidebar" aria-label="Módulos administrativos">
    <nav class="admin-sidebar-menu">
        <?php foreach ($opcionesAdmin as $opcion): ?>
            <a class="admin-sidebar-item <?= $rutaAdminActiva === $opcion['ruta'] ? 'admin-sidebar-active' : '' ?>" href="<?= BASE_URL ?>/index.php?ruta=<?= urlencode($opcion['ruta']) ?>" <?= $rutaAdminActiva === $opcion['ruta'] ? 'aria-current="page"' : '' ?>>
                <span class="admin-sidebar-icon" aria-hidden="true"><?= $opcion['icono'] ?></span>
                <span><?= htmlspecialchars($opcion['texto'], ENT_QUOTES, 'UTF-8') ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
    <a class="admin-sidebar-item admin-sidebar-logout" href="<?= BASE_URL ?>/index.php?ruta=admin-salir">
        <span class="admin-sidebar-icon" aria-hidden="true">&#8594;</span>
        <span>Cerrar sesión</span>
    </a>
</aside>
