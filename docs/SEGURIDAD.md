# Seguridad

## Uso de sesiones

- El proyecto inicia sesión en `index.php` con `session_start()`.
- Las API inician sesión en `api/helpers/respuesta.php` si no está activa.
- Los controladores `AlumnoController` y `PagoController` verifican `$_SESSION['id_alumno']` y `$_SESSION['rol'] === 'alumno'`.
- `AuthController::cerrarSesion()` destruye la sesión y borra la cookie correctamente.

## Consultas preparadas

- `app/models/Alumno.php` usa `prepare()` y `bind_param()`.
- `app/models/Adeudo.php` usa `prepare()` y `bind_param()`.
- `app/models/Pago.php` usa `prepare()` y `bind_param()`.
- `api/` reutiliza estos modelos para consultas seguras.

## Validación de formularios

- Cliente:
  - `public/js/script.js` valida que `matricula` y `password` no estén vacíos en el login.
  - Se generan y ocultan contraseñas en las vistas de creación de contraseña.
- Servidor:
  - `AuthController::autenticarAlumno()` verifica método POST, campos obligatorios y contraseña.
  - `AuthController::guardarPasswordAlumno()` valida campos, coincidencia de contraseña y fuerza básica de contraseña.
  - `PagoController::formulario()` valida método de pago permitido y estado del adeudo.
  - API valida métodos HTTP y cuerpo JSON en `api/helpers/respuesta.php` y `api/pagos/registrar.php`.

## Protección contra inyección SQL

- Uso de consultas preparadas en todos los modelos.
- No se construyen consultas SQL con concatenación de parámetros de usuario en los modelos revisados.
- Riesgo: en `config/conexion.php` no hay control adicional, pero la inyección SQL en consultas está mitigada.

## Protección contra XSS

- Vistas usan `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` en los campos mostrados.
- Sin embargo, algunas variables como `$_SESSION['rol']` no se imprimen en HTML.
- En `app/views/pago/formulario.php`, el mensaje de error se escapa con `htmlspecialchars`.
- El uso de `number_format()` y texto seguro reduce riesgos en los datos numéricos.

## Manejo de contraseñas

- Contraseña inicial se guarda con `password_hash(..., PASSWORD_DEFAULT)`.
- Verificación con `password_verify()`.
- `password_hash` es NULL hasta que el alumno crea su contraseña.
- `guardarPasswordHash()` sólo actualiza cuando `password_hash IS NULL`.
- Riesgo: el campo puede ser cadena vacía en la base de datos y entonces `tienePassword()` aún podría considerarlo inexistente.
- No hay recuperación o cambio de contraseña implementado.

## Control de acceso

- Solo el rol `alumno` puede acceder a rutas `alumno`, `pago` y `comprobante`.
- La API solo admite sesiones con `rol = alumno`.
- No hay control de acceso para administradores o QA, aunque las vistas existen.
- No hay verificación de CSRF en formularios.
- No hay configuración `Secure` o `SameSite` explícita en las cookies de sesión.

## Riesgos encontrados

- Ausencia de CSRF.
- Falta de protección `Secure` en cookies de sesión (depende del entorno HTTPS).
- No hay compactación de roles más allá de `alumno`.
- El endpoint `/api/pagos/registrar.php` no limita intentos ni protege contra reintentos múltiples.
- `password_hash` puede ser `NULL` o cadena vacía; la lógica asume NULL.
- El formulario `login-admin` y `login-qa` expone pantallas no seguras sin backend.

## Recomendaciones ordenadas por prioridad

1. Implementar CSRF en todos los formularios que modifican estado: login, crear contraseña, pago.
2. Configurar `session.cookie_secure`, `session.cookie_httponly` y `session.cookie_samesite` cuando se use HTTPS.
3. Añadir backend real para `login-admin` y `login-qa` o eliminar las vistas si no son funcionales.
4. Validar de forma estricta el `password_hash` antes de asumir que el alumno tiene contraseña.
5. Agregar límites de tasa y registro de acceso a la API.
6. Vincular `pagos` con `adeudos` mediante `id_adeudo` para mantener integridad.
7. Agregar validaciones de servidor más ricas y mensajes de error no expuestos.
