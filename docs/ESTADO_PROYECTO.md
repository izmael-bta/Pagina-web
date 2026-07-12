# Estado del Proyecto

## Funcionalidades totalmente implementadas

- Inicio de sesión de alumno mediante matrícula y contraseña.
  - `index.php`, `app/controllers/AuthController.php`, `app/views/auth/login.php`
- Creación de contraseña inicial para alumnos registrados.
  - `app/controllers/AuthController.php`, `app/views/auth/crear_password.php`
- Dashboard de alumno con datos personales y adeudo más reciente.
  - `app/controllers/AlumnoController.php`, `app/views/alumno/dashboard.php`
- Consulta del adeudo actual en la base de datos.
  - `app/models/Adeudo.php`, `app/views/alumno/dashboard.php`
- Simulación de registro de pago y comprobante.
  - `app/controllers/PagoController.php`, `app/views/pago/formulario.php`, `app/views/pago/comprobante.php`
- Registro de pagos en la tabla `pagos` y actualización del estado de adeudo a `Pagado`.
  - `app/models/Pago.php`, `app/models/Adeudo.php`
- API local autenticada por sesión con endpoints para perfil, adeudo actual, historial y registro de pago.
  - `api/alumnos/perfil.php`, `api/adeudos/actual.php`, `api/adeudos/historial.php`, `api/pagos/registrar.php`, `api/helpers/respuesta.php`
- Uso de sentencias preparadas en consultas SQL para `alumnos`, `adeudos` y `pagos`.
  - `app/models/Alumno.php`, `app/models/Adeudo.php`, `app/models/Pago.php`

## Funcionalidades parcialmente implementadas

- Registro de estudiante (`ruta=registro`) no es un formulario dinámico ni persistente.
  - `app/controllers/AlumnoController.php`, `app/views/alumno/registro.php`
- Autenticación vía API solo con sesión PHP, no con token independiente.
  - `api/helpers/respuesta.php`
- El formulario de pago valida método de pago y estado del adeudo, pero no integra un proveedor real.
  - `app/controllers/PagoController.php`, `app/views/pago/formulario.php`

## Funcionalidades planeadas pero no existentes en el código

- Login funcional de administrador.
  - `app/views/auth/login-admin.php` muestra interfaz, pero no hay lógica POST ni validación en `AuthController.php`.
- Login funcional de Control de Calidad (QA).
  - `app/views/auth/login-qa.php` sin backend asociado.
- CRUD completo para alumnos, adeudos y pagos.
  - No existen rutas ni controladores que creen, editen o eliminen registros desde la interfaz.
- Historial de adeudos en la interfaz de usuario.
  - Existe historial en la API (`api/adeudos/historial.php`), pero no en las vistas HTML.
- Control de acceso granular para roles distintos de `alumno`.
  - El proyecto solo reconoce el rol `alumno` en sesión.

## Archivos con errores o riesgos

- `app/views/auth/login-admin.php` y `app/views/auth/login-qa.php`
  - Riesgo: formularios presentan interfaz sin funcionamiento real.
- `app/controllers/AuthController.php`
  - Potencial inconsistencia de actualización de `password_hash` si en la base de datos el campo es cadena vacía en lugar de NULL.
- `database/migrations/003_asignar_periodo_julio_2026_adeudos_existentes.sql`
  - Indicada como migración condicional. Debe ejecutarse solo con confirmación, ya que asigna `2026-07-01` a adeudos existentes con periodo NULL.
- `app/models/Pago.php`
  - No relaciona la tabla `pagos` con `adeudos` mediante `id_adeudo`.

## Trabajo pendiente

- Implementar backend real para `login-admin` y `login-qa`.
- Añadir vista de historial de adeudos para el alumno.
- Extender la base de datos para vincular `pagos` con `adeudos` y evitar ambigüedad.
- Agregar soporte de CRUD para administración de alumnos, adeudos y pagos.
- Incorporar CSRF y seguridad de cookies (`Secure`, `SameSite`, `HttpOnly` cuando aplique).
- Crear validaciones de servidor más explícitas para la API y formularios.

## Porcentaje aproximado de avance

- Avance estimado: **70%**.
- Criterio: el núcleo de la experiencia del alumno está implementado (login, contraseña, consulta de adeudo, pago simulado, comprobante y API básica), pero faltan funcionalidades administrativas, historial UI, y varios componentes de gestión de usuarios.
