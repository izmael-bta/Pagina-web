# Inventario de Archivos

## Archivos y propósito

### Root
- `index.php`
  - Front controller y enrutador principal.
- `README.md`
  - Documentación general del proyecto existente.

### config
- `config/conexion.php`
  - Conexión MySQL con `mysqli` y configuración `utf8`.

### app/controllers
- `app/controllers/AuthController.php`
  - Autenticación de alumno, creación de contraseña y gestión de sesión.
- `app/controllers/AlumnoController.php`
  - Dashboard del alumno y página de registro demostrativo.
- `app/controllers/PagoController.php`
  - Formulario de pago, registro en transacción y comprobante.

### app/models
- `app/models/Alumno.php`
  - Consultas y actualizaciones de la tabla `alumnos`.
- `app/models/Adeudo.php`
  - Consulta de adeudo actual/historial y actualización de estado.
- `app/models/Pago.php`
  - Inserción de registros en la tabla `pagos`.

### app/helpers
- `app/helpers/PeriodoHelper.php`
  - Formatea fechas de periodo a texto legible.

### app/views
- `app/views/auth/login.php`
  - Interfaz de login de alumno.
- `app/views/auth/crear_password.php`
  - Interfaz para crear contraseña inicial.
- `app/views/auth/login-admin.php`
  - Interfaz de acceso de administrador (sin backend).
- `app/views/auth/login-qa.php`
  - Interfaz de acceso de QA (sin backend).
- `app/views/alumno/dashboard.php`
  - Vista principal del alumno con adeudo.
- `app/views/alumno/registro.php`
  - Actividad de registro demostrativo con datos fijos.
- `app/views/pago/formulario.php`
  - Formulario para seleccionar método de pago.
- `app/views/pago/comprobante.php`
  - Comprobante de pago.
- `app/views/partials/navbar_publica.php`
  - Barra de navegación pública.
- `app/views/partials/navbar_alumno.php`
  - Barra de navegación privada para alumnos.
- `app/views/partials/flash.php`
  - Mensajes flash.

### api
- `api/helpers/respuesta.php`
  - Helper de API: sesión, validaciones, respuestas JSON.
- `api/alumnos/perfil.php`
  - Endpoint GET de perfil de alumno.
- `api/adeudos/actual.php`
  - Endpoint GET de adeudo actual.
- `api/adeudos/historial.php`
  - Endpoint GET de historial de adeudos.
- `api/pagos/registrar.php`
  - Endpoint POST para registrar pago.

### public
- `public/css/estilos.css`
  - Estilos globales y de componentes.
- `public/js/script.js`
  - Interacción de menú accesos, visibilidad de contraseña y generación de contraseña segura.
- `public/img/logo.png`
  - Imagen del logo de la institución.

### database
- `database/database.sql`
  - Esquema completo y datos iniciales.
- `database/migrations/001_agregar_password_alumnos.sql`
  - Migración para agregar `password_hash` a `alumnos`.
- `database/migrations/002_agregar_periodo_adeudos.sql`
  - Migración para agregar `periodo` a `adeudos`.
- `database/migrations/003_asignar_periodo_julio_2026_adeudos_existentes.sql`
  - Migración condicional para backfill de periodos NULL.

### docs
- `docs/openapi.yaml`
  - Especificación OpenAPI de la API.
- `docs/pruebas-postman.md`
  - Casos de prueba manuales orientados a Postman.
- `docs/README.md`
  - Documentación general generada.
- `docs/ESTADO_PROYECTO.md`
  - Estado de implementación y riesgos.
- `docs/ARQUITECTURA.md`
  - Arquitectura real y diagramas Mermaid.
- `docs/BASE_DATOS.md`
  - Modelo de datos y análisis de normalización.
- `docs/FUNCIONALIDADES.md`
  - Funcionalidades implementadas y archivos relacionados.
- `docs/API.md`
  - Documentación de endpoints reales y propuestos.
- `docs/SEGURIDAD.md`
  - Evaluación de seguridad y recomendaciones.
- `docs/PRUEBAS.md`
  - Casos de prueba estructurados.
- `docs/REQUERIMIENTOS.md`
  - Requerimientos funcionales y trazabilidad.
- `docs/INVENTARIO_ARCHIVOS.md`
  - Inventario de archivos y propósito.

## Dependencias entre archivos

- `index.php` depende de `config/conexion.php` y los controladores en `app/controllers/`.
- Controladores dependen de modelos y vistas.
- Vistas dependen de `BASE_URL` y parte de los datos preparados por controladores.
- `api/*` dependen de `api/helpers/respuesta.php`, `config/conexion.php`, y modelos.
- `app/models` dependen de la conexión `$conn` de `config/conexion.php`.
- `app/views/auth/crear_password.php` y `app/views/auth/login.php` dependen de `app/views/partials/flash.php`.

## Archivos duplicados, obsoletos o sin utilizar

- No se detectan archivos duplicados funcionales.
- `app/views/auth/login-admin.php` y `app/views/auth/login-qa.php` están sin utilizar por falta de backend.
- `app/views/alumno/registro.php` está presente como actividad demostrativa, no como funcionalidad persistente.
- `database/migrations/003_asignar_periodo_julio_2026_adeudos_existentes.sql` es condicional y debe usarse con precaución.
