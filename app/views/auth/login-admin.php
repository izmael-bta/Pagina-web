<?php
// Vista: formulario visual del administrador dentro del layout compartido.
$tituloAcceso = 'Acceso de Administrador';
$tituloBienvenida = 'Bienvenido(a) Administrador';
$descripcion = 'Accede al sistema para gestionar alumnos, adeudos, pagos y servicios del Portal Web de Pagos UTSC.';
$textoSeguridad = 'Acceso seguro y autorizado';
$rolActual = 'login-admin';

ob_start();
?>
<?php require __DIR__ . '/../partials/flash.php'; ?>
<form method="post" action="<?= BASE_URL ?>/index.php?ruta=autenticar-admin">
    <div class="auth-field">
        <label for="admin-correo">Correo institucional</label>
        <input type="email" id="admin-correo" name="correo" autocomplete="username" required>
    </div>
    <div class="auth-field">
        <label for="admin-contrasena">Contraseña</label>
        <div class="campo-password">
            <input type="password" id="admin-contrasena" name="password" autocomplete="current-password" required>
            <button class="boton-toggle-password" type="button" data-password-target="admin-contrasena" aria-label="Mostrar contraseña" aria-pressed="false">Mostrar</button>
        </div>
    </div>
    <button class="auth-submit" type="submit">Ingresar</button>
</form>
<a class="auth-return-link" href="<?= BASE_URL ?>/index.php?ruta=login">Regresar al acceso del alumno</a>
<?php
$contenidoFormulario = ob_get_clean();
require __DIR__ . '/../partials/auth_layout.php';
