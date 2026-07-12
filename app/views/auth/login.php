<?php
// Vista: formulario de acceso del alumno dentro del layout compartido.
$tituloAcceso = 'Acceso de Alumno';
$tituloBienvenida = 'Bienvenido(a) Alumno';
$descripcion = 'Consulta tus adeudos, realiza tus pagos y obtén tus comprobantes desde el Portal Web de Pagos UTSC.';
$textoSeguridad = 'Acceso seguro para alumnos registrados';
$rolActual = 'login';

ob_start();
require __DIR__ . '/../partials/flash.php';
?>
<form method="post" action="<?= BASE_URL ?>/index.php?ruta=autenticar-alumno" id="form-login">
    <div class="auth-field">
        <label for="matricula">Matrícula</label>
        <input type="text" id="matricula" name="matricula" placeholder="Ingresa tu matrícula" autocomplete="username" required>
    </div>
    <div class="auth-field">
        <label for="password-alumno">Contraseña</label>
        <div class="campo-password">
            <input type="password" id="password-alumno" name="password" autocomplete="current-password" required>
            <button class="boton-toggle-password" type="button" data-password-target="password-alumno" aria-label="Mostrar contraseña" aria-pressed="false">Mostrar</button>
        </div>
    </div>
    <button class="auth-submit" type="submit">Ingresar</button>
</form>
<a class="auth-return-link" href="<?= BASE_URL ?>/index.php?ruta=crear-password">¿No tienes contraseña? Crear contraseña</a>
<?php
$contenidoFormulario = ob_get_clean();
require __DIR__ . '/../partials/auth_layout.php';
