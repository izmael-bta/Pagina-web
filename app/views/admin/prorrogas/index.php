<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Prórrogas</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css">
</head>
<body class="admin-page">
    <?php require __DIR__ . '/../partials/navbar.php'; ?>
    <div class="admin-layout">
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>
        <div class="admin-sidebar-overlay" id="admin-sidebar-overlay" hidden></div>
        <main class="admin-main">
            <header class="admin-debt-header">
                <div>
                    <h1>Gestión de Prórrogas</h1>
                    <p>Consulta y extiende las fechas límite de los adeudos pendientes.</p>
                </div>
                <a class="admin-quick-action" href="<?= BASE_URL ?>/index.php?ruta=admin-prorroga-crear">Aplicar prórroga</a>
            </header>

            <?php require __DIR__ . '/../../partials/flash.php'; ?>

            <form class="admin-debt-toolbar" method="get" action="<?= BASE_URL ?>/index.php">
                <input type="hidden" name="ruta" value="admin-prorrogas">
                <label>Buscar
                    <input type="search" name="buscar" value="<?= htmlspecialchars($b, ENT_QUOTES, 'UTF-8') ?>" placeholder="Matrícula, nombre o correo">
                </label>
                <label>Periodo
                    <input type="month" name="periodo" value="<?= htmlspecialchars($p, ENT_QUOTES, 'UTF-8') ?>">
                </label>
                <label>Estado
                    <select name="estado">
                        <option value="">Todos</option>
                        <?php foreach (['Vigente', 'Vencida', 'Finalizada'] as $opcion): ?>
                            <option value="<?= $opcion ?>" <?= $e === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button class="admin-student-submit" type="submit">Filtrar</button>
            </form>

            <div class="admin-debt-table-wrap">
                <table class="admin-debt-table">
                    <thead>
                        <tr>
                            <th>Matrícula</th><th>Alumno</th><th>Periodo</th><th>Fecha anterior</th><th>Nueva fecha</th>
                            <th>Total</th><th>Estado</th><th>Autorizada por</th><th>Aplicación</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($prorrogas === []): ?>
                            <tr><td class="admin-debt-empty" colspan="10">No hay prórrogas registradas con los criterios seleccionados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($prorrogas as $prorroga): ?>
                                <tr>
                                    <td><?= htmlspecialchars($prorroga['matricula'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($prorroga['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars(PeriodoHelper::formatear($prorroga['periodo']), ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($prorroga['fecha_limite_anterior'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($prorroga['nueva_fecha_limite'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>$<?= number_format((float) $prorroga['total'], 2) ?></td>
                                    <td><span class="admin-extension-status admin-extension-status-<?= strtolower($prorroga['estado_visual']) ?>"><?= htmlspecialchars($prorroga['estado_visual'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                    <td><?= htmlspecialchars($prorroga['administrador'] ?? 'No disponible', ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($prorroga['fecha_aplicacion'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td class="admin-debt-actions"><a href="<?= BASE_URL ?>/index.php?ruta=admin-prorroga-ver&amp;id=<?= (int) $prorroga['id_prorroga'] ?>">Ver</a></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php $parametros = ['ruta' => 'admin-prorrogas', 'buscar' => $b, 'periodo' => $p, 'estado' => $e]; ?>
            <nav class="admin-debt-pagination" aria-label="Paginación de prórrogas">
                <span>Total de resultados: <?= $total ?></span>
                <?php if ($pg > 1): ?><a href="<?= BASE_URL ?>/index.php?<?= htmlspecialchars(http_build_query($parametros + ['pagina' => $pg - 1]), ENT_QUOTES, 'UTF-8') ?>">Anterior</a><?php endif; ?>
                <span>Página <?= $pg ?> de <?= $paginas ?></span>
                <?php if ($pg < $paginas): ?><a href="<?= BASE_URL ?>/index.php?<?= htmlspecialchars(http_build_query($parametros + ['pagina' => $pg + 1]), ENT_QUOTES, 'UTF-8') ?>">Siguiente</a><?php endif; ?>
            </nav>
            <?php require __DIR__ . '/../partials/footer.php'; ?>
        </main>
    </div>
    <script src="<?= BASE_URL ?>/public/js/admin.js"></script>
</body>
</html>
