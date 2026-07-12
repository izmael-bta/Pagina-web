# Funcionalidades

## Flujo del alumno

1. Accede a `index.php?ruta=login`.
2. Ingresa matrícula y contraseña.
3. Si el login es exitoso, se redirige a `index.php?ruta=alumno`.
4. El dashboard muestra datos personales y el adeudo más reciente.
5. Si el adeudo no está pagado, el alumno puede ir a `index.php?ruta=pago`.
6. En el formulario de pago selecciona método y confirma.
7. El sistema registra un pago y marca el adeudo como pagado.
8. Se muestra el comprobante en `index.php?ruta=comprobante`.
9. Puede cerrar sesión con `index.php?ruta=salir`.

### Archivos relacionados

- `index.php`
- `app/controllers/AuthController.php`
- `app/controllers/AlumnoController.php`
- `app/controllers/PagoController.php`
- `app/models/Alumno.php`
- `app/models/Adeudo.php`
- `app/models/Pago.php`
- `app/views/auth/login.php`
- `app/views/alumno/dashboard.php`
- `app/views/pago/formulario.php`
- `app/views/pago/comprobante.php`

## Flujo del administrador

- `app/views/auth/login-admin.php` existe como formulario de acceso.
- No hay backend funcional en `AuthController.php` ni ruta POST de administración.
- Conclusión: el flujo de administrador está planteado en la UI, pero no implementado en el código.

## Acceso mediante matrícula

- Válido sólo para alumnos.
- `AuthController::autenticarAlumno()` utiliza `$_POST['matricula']`.
- `Alumno::buscarPorMatricula()` recupera datos según la matrícula.
- `AuthController` guarda en sesión `id_alumno`, `matricula`, `nombre` y `rol = 'alumno'`.

## Consulta de datos

- `AlumnoController::dashboard()` obtiene alumno con `Alumno::buscarPorId()`.
- Los datos mostrados son `matricula`, `nombre`, `correo`, `carrera`, `grupo`.
- Interfaz de alumno: `app/views/alumno/dashboard.php`.

## Consulta de adeudos

- `AlumnoController::dashboard()` obtiene el último adeudo con `Adeudo::obtenerUltimoPorAlumno()`.
- El adeudo muestra `periodo`, `mensualidad`, `aportacion_tsu`, `recargo`, `total`, `estado`.
- API:
  - `api/adeudos/actual.php` devuelve el adeudo más reciente.
  - `api/adeudos/historial.php` devuelve todos los adeudos del alumno.

## Registro de pagos

- `PagoController::formulario()` valida método de pago y estado del adeudo.
- Si el adeudo no está pagado, se crea un folio y se registra el pago en `pagos`.
- `Pago::registrar()` inserta el pago.
- `Adeudo::marcarComoPagado()` actualiza el estado del adeudo.
- La operación usa transacción MySQL.

## Comprobantes

- `PagoController::comprobante()` carga `app/views/pago/comprobante.php`.
- Muestra `nombre`, `matricula`, `metodo_pago`, `total_pagado`, `folio`, `fecha_pago`.
- La información se extrae de `$_SESSION` tras el registro del pago.

## Historial

- Existe el endpoint `api/adeudos/historial.php`.
- No hay vista HTML de historial en `app/views/`.
- La funcionalidad de historial está implementada solo en la API.

## Cierre de sesión

- `AuthController::cerrarSesion()` destruye la sesión, borra la cookie y redirige a login.
- Ruta: `index.php?ruta=salir`.

## CRUD disponible

- No existe CRUD completo para:
  - Alumnos
  - Adeudos
  - Pagos
- Solo hay operaciones básicas implementadas:
  - Lectura de alumno y adeudo.
  - Actualización de contraseña inicial.
  - Inserción de pago.
  - Actualización de estado de adeudo.

## Observaciones importantes

- `app/views/alumno/registro.php` se presenta como actividad de registro, pero no crea ni almacena datos reales en la base de datos.
- Las rutas de administrador y QA son solamente formularios de acceso en la interfaz, sin lógica correspondiente.
