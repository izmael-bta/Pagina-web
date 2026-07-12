<?php // Vista: listado administrativo de alumnos. ?>
<!DOCTYPE html><html lang="es"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Alumnos</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css">
</head><body class="admin-page">
<?php require __DIR__ . '/../partials/navbar.php'; ?>
<div class="admin-layout">
<?php require __DIR__ . '/../partials/sidebar.php'; ?><div class="admin-sidebar-overlay" id="admin-sidebar-overlay" hidden></div>
<main class="admin-main">
    <header class="admin-student-header">
        <div><h1>Gestión de Alumnos</h1><p>Consulta, registra y administra los alumnos del portal.</p></div>
        <a class="admin-quick-action" href="<?= BASE_URL ?>/index.php?ruta=admin-alumno-crear">Registrar alumno</a>
    </header>
    <?php require __DIR__ . '/../../partials/flash.php'; ?>
    <form class="admin-student-toolbar" method="get" action="<?= BASE_URL ?>/index.php">
        <input type="hidden" name="ruta" value="admin-alumnos">
        <label class="admin-student-search">Buscar
            <input type="search" name="buscar" value="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>" placeholder="Matrícula, nombre o correo">
        </label>
        <label class="admin-student-filter">Estado
            <select name="estado">
                <option value="">Todos</option>
                <option value="Activo" <?= $estado === 'Activo' ? 'selected' : '' ?>>Activo</option>
                <option value="Inactivo" <?= $estado === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </label>
        <button class="admin-student-submit" type="submit">Aplicar filtros</button>
    </form>
    <div class="admin-student-table-wrap">
        <table class="admin-student-table">
            <thead><tr><th>Matrícula</th><th>Nombre</th><th>Correo</th><th>Carrera</th><th>Grupo</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($alumnos as $fila): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['matricula'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($fila['correo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($fila['carrera'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($fila['grupo'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="admin-student-status <?= $fila['estado'] === 'Activo' ? 'admin-student-status-active' : 'admin-student-status-inactive' ?>"><?= htmlspecialchars($fila['estado'], ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td><div class="admin-student-actions">
                        <a href="<?= BASE_URL ?>/index.php?ruta=admin-alumno-editar&amp;id=<?= (int) $fila['id_alumno'] ?>">Editar</a>
                        <?php if ($fila['estado'] === 'Activo'): ?>
                            <form method="post" action="<?= BASE_URL ?>/index.php?ruta=admin-alumno-desactivar" data-confirm="¿Deseas desactivar a este alumno? No podrá iniciar sesión, pero sus adeudos, pagos y comprobantes se conservarán.">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id_alumno" value="<?= (int) $fila['id_alumno'] ?>">
                                <button type="submit">Desactivar</button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="<?= BASE_URL ?>/index.php?ruta=admin-alumno-reactivar">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="id_alumno" value="<?= (int) $fila['id_alumno'] ?>">
                                <button type="submit">Reactivar</button>
                            </form>
                        <?php endif; ?>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($alumnos === []): ?><p class="admin-student-empty">No se encontraron alumnos con los criterios seleccionados.</p><?php endif; ?>
    </div>
    <?php
    $parametros = ['ruta' => 'admin-alumnos', 'buscar' => $busqueda, 'estado' => $estado];
    ?>
    <nav class="admin-student-pagination" aria-label="Paginación de alumnos">
        <span>Total de resultados: <?= $totalResultados ?></span>
        <?php if ($pagina > 1): ?><a href="<?= BASE_URL ?>/index.php?<?= htmlspecialchars(http_build_query($parametros + ['pagina' => $pagina - 1]), ENT_QUOTES, 'UTF-8') ?>">Anterior</a><?php endif; ?>
        <span>Página <?= $pagina ?> de <?= $totalPaginas ?></span>
        <?php if ($pagina < $totalPaginas): ?><a href="<?= BASE_URL ?>/index.php?<?= htmlspecialchars(http_build_query($parametros + ['pagina' => $pagina + 1]), ENT_QUOTES, 'UTF-8') ?>">Siguiente</a><?php endif; ?>
    </nav>
    <?php require __DIR__ . '/../partials/footer.php'; ?>
</main></div><script src="<?= BASE_URL ?>/public/js/admin.js"></script></body></html>
