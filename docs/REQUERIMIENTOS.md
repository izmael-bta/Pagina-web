# Requerimientos

## Requerimientos funcionales identificados

- RF001: El alumno debe iniciar sesión con matrícula y contraseña.
  - Implementado en: `app/controllers/AuthController.php`, `app/views/auth/login.php`, `index.php`.
- RF002: El alumno debe poder crear su contraseña inicial usando matrícula y correo registrado.
  - Implementado en: `app/controllers/AuthController.php`, `app/views/auth/crear_password.php`.
- RF003: El sistema debe mostrar los datos personales del alumno autenticado.
  - Implementado en: `app/controllers/AlumnoController.php`, `app/views/alumno/dashboard.php`.
- RF004: El sistema debe mostrar el adeudo más reciente del alumno.
  - Implementado en: `app/models/Adeudo.php`, `app/controllers/AlumnoController.php`, `app/views/alumno/dashboard.php`.
- RF005: El alumno debe poder seleccionar método de pago y registrar el pago.
  - Implementado en: `app/controllers/PagoController.php`, `app/views/pago/formulario.php`, `app/models/Pago.php`, `app/models/Adeudo.php`.
- RF006: El sistema debe mostrar un comprobante del pago registrado.
  - Implementado en: `app/controllers/PagoController.php`, `app/views/pago/comprobante.php`.
- RF007: El alumno debe poder cerrar sesión.
  - Implementado en: `app/controllers/AuthController.php`, `index.php`.
- RF008: La API debe exponer perfil, adeudo actual, historial y registro de pago autenticados por sesión.
  - Implementado en: `api/`, `api/helpers/respuesta.php`, `app/models/`.

## Requerimientos no funcionales

- RNF001: Todas las consultas de base de datos deben usar consultas preparadas.
  - Implementado en: `app/models/Alumno.php`, `app/models/Adeudo.php`, `app/models/Pago.php`.
- RNF002: Las vistas deben escapar datos de usuario para prevenir XSS.
  - Parcialmente implementado en: `app/views/`, con uso de `htmlspecialchars()` en las vistas principales.
- RNF003: El sistema debe ejecutarse en un entorno XAMPP con Apache y MySQL.
  - Implementado en: `README.md`, `config/conexion.php`.
- RNF004: La API debe responder JSON con `Content-Type: application/json`.
  - Implementado en: `api/helpers/respuesta.php`.

## Reglas de negocio

- RB001: Un alumno solo puede acceder a rutas y API si `$_SESSION['rol'] === 'alumno'`.
  - Implementado en: `AlumnoController.php`, `PagoController.php`, `api/helpers/respuesta.php`.
- RB002: Un alumno solo puede registrar un pago si el adeudo está en estado diferente a `Pagado`.
  - Implementado en: `PagoController.php`, `api/pagos/registrar.php`.
- RB003: Un alumno no puede iniciar sesión si no tiene contraseña creada.
  - Implementado en: `AuthController::autenticarAlumno()`.
- RB004: `password_hash` solo se crea si actualmente es `NULL`.
  - Implementado en: `Alumno::guardarPasswordHash()`.

## Matriz de trazabilidad

- RF001 → Login de alumno → `app/controllers/AuthController.php`, `app/views/auth/login.php`, `index.php`
- RF002 → Crear contraseña inicial → `app/controllers/AuthController.php`, `app/views/auth/crear_password.php`
- RF003 → Dashboard alumno → `app/controllers/AlumnoController.php`, `app/views/alumno/dashboard.php`
- RF004 → Consulta de adeudo → `app/models/Adeudo.php`, `app/controllers/AlumnoController.php`
- RF005 → Pago de adeudo → `app/controllers/PagoController.php`, `app/models/Pago.php`, `app/models/Adeudo.php`, `app/views/pago/formulario.php`
- RF006 → Comprobante → `app/controllers/PagoController.php`, `app/views/pago/comprobante.php`
- RF008 → API autenticada → `api/`, `api/helpers/respuesta.php`

## Checklist

- [x] RF001: implementado
- [x] RF002: implementado
- [x] RF003: implementado
- [x] RF004: implementado
- [x] RF005: implementado
- [x] RF006: implementado
- [x] RF007: implementado
- [x] RF008: implementado
- [ ] Admin/QA login: pendiente
- [ ] Historial de adeudos UI: pendiente
- [ ] CRUD de administración: pendiente
- [ ] CSRF: pendiente
- [ ] Vínculo `pagos` ↔ `adeudos`: pendiente
