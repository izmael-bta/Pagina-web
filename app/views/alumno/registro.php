<?php // Vista: actividad de registro del estudiante. ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Estudiante</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/estilos.css">
</head>
<body class="registro-page">
    <div class="contenedor registro-estudiante">
        <h1>Registro de Estudiante</h1>
        <div class="dato"><strong>Nombre:</strong> <?= htmlspecialchars($estudiante['nombre'], ENT_QUOTES, 'UTF-8') ?></div>
        <div class="dato"><strong>Matrícula:</strong> <?= htmlspecialchars($estudiante['matricula'], ENT_QUOTES, 'UTF-8') ?></div>
        <div class="dato"><strong>Calificación:</strong> <?= (int) $estudiante['calificacion'] ?></div>
        <p class="<?= $aprobo ? 'aprobado' : 'reprobado' ?>">
            Resultado: <?= $aprobo ? 'Aprobado' : 'Reprobado' ?>
        </p>
    </div>
</body>
</html>
