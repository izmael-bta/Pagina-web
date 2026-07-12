<?php
// Vista: activación inicial de contraseña dentro del layout compartido.
$tituloAcceso = 'Crear contraseña de alumno';
$tituloBienvenida = 'Crea tu acceso al Portal';
$descripcion = 'Verifica tus datos registrados y crea una contraseña segura para consultar tus adeudos, realizar pagos y obtener tus comprobantes.';
$textoSeguridad = 'Activación segura para alumnos registrados';
$rolActual = 'login';

ob_start();
?>
<p class="auth-password-intro">Ingresa los datos registrados en el sistema para activar tu acceso.</p>
<?php require __DIR__ . '/../partials/flash.php'; ?>
<form class="auth-password-form" method="post" action="<?= BASE_URL ?>/index.php?ruta=guardar-password" id="form-crear-password">
    <div class="auth-field">
        <label for="crear-matricula">Matrícula</label>
        <input type="text" id="crear-matricula" name="matricula" autocomplete="username" required>
    </div>

    <div class="auth-field">
        <label for="correo">Correo registrado</label>
        <input type="email" id="correo" name="correo" autocomplete="email" required>
    </div>

    <div class="auth-password-grid">
        <div class="auth-field">
            <label for="nueva-password">Nueva contraseña</label>
            <div class="campo-password">
                <input type="password" id="nueva-password" name="password" autocomplete="new-password" minlength="8" required>
                <button class="boton-toggle-password" type="button" data-password-target="nueva-password" aria-label="Mostrar contraseña" aria-pressed="false">Mostrar</button>
            </div>
        </div>

        <div class="auth-field">
            <label for="confirmar-password">Confirmar contraseña</label>
            <div class="campo-password">
                <input type="password" id="confirmar-password" name="confirmar_password" autocomplete="new-password" minlength="8" required>
                <button class="boton-toggle-password" type="button" data-password-target="confirmar-password" aria-label="Mostrar contraseña" aria-pressed="false">Mostrar</button>
            </div>
        </div>
    </div>

    <p class="auth-password-help">Usa 12 caracteres o más con mayúscula, minúscula, número y carácter especial.</p>
    <button class="auth-submit" type="submit">Crear contraseña</button>
</form>
<a class="auth-return-link" href="<?= BASE_URL ?>/index.php?ruta=login">Regresar al inicio de sesión</a>
<?php
$contenidoFormulario = ob_get_clean();
require __DIR__ . '/../partials/auth_layout.php';
