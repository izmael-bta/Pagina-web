<?php
// Vista parcial: navegación privada determinada por la sesión del alumno.
$identificadorAlumno = $_SESSION['matricula']
    ?? ($alumno['matricula'] ?? $_SESSION['nombre'] ?? '');
?>
<nav class="navbar navbar-privada" aria-label="Navegación del alumno">
    <div class="navbar-contenido">
        <a class="navbar-marca" href="<?= BASE_URL ?>/index.php?ruta=alumno">
            Portal Web de Pagos UTSC
        </a>
        <div class="navbar-alumno-acciones">
            <span class="navbar-alumno-identidad">
                Alumno: <?= htmlspecialchars((string) $identificadorAlumno, ENT_QUOTES, 'UTF-8') ?>
            </span>
            <a class="boton-cerrar-sesion" href="<?= BASE_URL ?>/index.php?ruta=salir">Cerrar sesión</a>
        </div>
    </div>
</nav>
