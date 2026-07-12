<?php
// Vista: formulario visual de QA dentro del layout compartido.
$tituloAcceso = 'Acceso de Control de Calidad';
$tituloBienvenida = 'Bienvenido(a) Control de Calidad';
$descripcion = 'Accede al módulo para validar funcionalidades, ejecutar pruebas y registrar incidencias del sistema.';
$textoSeguridad = 'Validación y seguimiento de calidad';
$rolActual = 'login-qa';

ob_start();
?>
<form method="post" action="<?= BASE_URL ?>/index.php?ruta=login-qa">
    <div class="auth-field">
        <label for="qa-usuario">Usuario o correo</label>
        <input type="text" id="qa-usuario" name="usuario" autocomplete="username" required>
    </div>
    <div class="auth-field">
        <label for="qa-contrasena">Contraseña</label>
        <div class="campo-password">
            <input type="password" id="qa-contrasena" name="contrasena" autocomplete="current-password" required>
            <button class="boton-toggle-password" type="button" data-password-target="qa-contrasena" aria-label="Mostrar contraseña" aria-pressed="false">Mostrar</button>
        </div>
    </div>
    <button class="auth-submit" type="submit">Ingresar</button>
</form>
<a class="auth-return-link" href="<?= BASE_URL ?>/index.php?ruta=login">Regresar al acceso del alumno</a>
<?php
$contenidoFormulario = ob_get_clean();
require __DIR__ . '/../partials/auth_layout.php';
