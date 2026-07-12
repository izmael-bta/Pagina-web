<?php
// Vista parcial: estructura visual común para los accesos públicos.
$rutaActiva = $rolActual;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tituloAcceso, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/Portal_Web_Pagos_UTSC/public/css/estilos.css">
    <link rel="stylesheet" href="/Portal_Web_Pagos_UTSC/public/css/auth.css">
</head>
<body class="auth-public-page">
    <?php require __DIR__ . '/navbar_publica.php'; ?>
    <main id="auth-screen" class="auth-screen">
        <section class="auth-layout">
            <div class="auth-information" aria-labelledby="auth-bienvenida">
                <img src="/Portal_Web_Pagos_UTSC/public/img/logo.png" alt="Universidad Tecnológica Santa Catarina" class="auth-university-logo">
                <h1 class="auth-welcome-title" id="auth-bienvenida"><?= htmlspecialchars($tituloBienvenida, ENT_QUOTES, 'UTF-8') ?></h1>
                <div class="auth-title-divider" aria-hidden="true"></div>
                <p class="auth-role-description"><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></p>
                <p class="auth-security-text"><?= htmlspecialchars($textoSeguridad, ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div class="auth-form-area" aria-labelledby="auth-titulo-acceso">
                <div class="auth-form-card">
                    <h2 id="auth-titulo-acceso"><?= htmlspecialchars($tituloAcceso, ENT_QUOTES, 'UTF-8') ?></h2>
                    <?= $contenidoFormulario ?>
                </div>
            </div>
        </section>
    </main>
    <script src="<?= BASE_URL ?>/public/js/script.js"></script>
</body>
</html>
