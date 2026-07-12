# Pruebas

## Casos de prueba

### ID: P001
- Módulo: Autenticación de alumno
- Precondiciones: alumno existente con contraseña creada.
- Pasos:
  1. Abrir `index.php?ruta=login`.
  2. Ingresar matrícula `25906`.
  3. Ingresar contraseña correcta.
  4. Enviar formulario.
- Datos de entrada:
  - matrícula = `25906`
  - contraseña = valor desconocido (hash presente en DB).
- Resultado esperado: redirección a `index.php?ruta=alumno`, sesión iniciada.
- Estado: `no ejecutado`.

### ID: P002
- Módulo: Creación de contraseña
- Precondiciones: alumno existente sin contraseña (`password_hash IS NULL`).
- Pasos:
  1. Abrir `index.php?ruta=crear-password`.
  2. Ingresar matrícula válida y correo registrado.
  3. Ingresar contraseña segura y confirmación.
  4. Enviar formulario.
- Datos de entrada:
  - matrícula = `25918`
  - correo = `25918@virtual.utsc.edu.mx`
  - contraseña = `Ejemplo@1234`
- Resultado esperado: redirección a login con mensaje de éxito.
- Estado: `no ejecutado`.

### ID: P003
- Módulo: Consulta de adeudo
- Precondiciones: inicio de sesión de alumno con adeudo pendiente.
- Pasos:
  1. Iniciar sesión.
  2. Verificar información de adeudo en `index.php?ruta=alumno`.
- Datos de entrada: alumno con matrícula `25906` o `25918`.
- Resultado esperado: muestra adeudo con periodo `2026-07-01` y estado correcto según la base de datos.
- Estado: `no ejecutado`.

### ID: P004
- Módulo: Pago de adeudo
- Precondiciones: alumno con adeudo pendiente.
- Pasos:
  1. Iniciar sesión.
  2. Ir a `index.php?ruta=pago`.
  3. Seleccionar `Tarjeta` o `Efectivo`.
  4. Enviar formulario.
- Datos de entrada:
  - `metodo_pago` = `Tarjeta`
- Resultado esperado: registro del pago y redirección a comprobante con folio y fecha.
- Estado: `no ejecutado`.

### ID: P005
- Módulo: Cierre de sesión
- Precondiciones: sesión de alumno activa.
- Pasos:
  1. Iniciar sesión.
  2. Seleccionar `Cerrar sesión`.
- Resultado esperado: redirección a login y destrucción de sesión.
- Estado: `no ejecutado`.

### ID: P006
- Módulo: API perfil sin sesión
- Precondiciones: ausencia de cookie `PHPSESSID`.
- Pasos:
  1. Enviar `GET /api/alumnos/perfil.php` sin cookies.
- Resultado esperado: HTTP 401, `exito: false`.
- Resultado obtenido: HTTP 401.
- Estado: `aprobado`.

### ID: P007
- Módulo: API adeudo actual con sesión
- Precondiciones: sesión válida de alumno.
- Pasos:
  1. Enviar `GET /api/adeudos/actual.php` con cookie de sesión.
- Resultado esperado: HTTP 200 y objeto `adeudo`.
- Estado: `no ejecutado`.

### ID: P008
- Módulo: API historial de adeudos con sesión
- Precondiciones: sesión válida de alumno.
- Pasos:
  1. Enviar `GET /api/adeudos/historial.php` con cookie de sesión.
- Resultado esperado: HTTP 200 y arreglo `adeudos`.
- Estado: `no ejecutado`.

### ID: P009
- Módulo: API registrar pago con método inválido
- Precondiciones: sesión válida de alumno.
- Pasos:
  1. Enviar `POST /api/pagos/registrar.php` con `{ "metodo_pago": "Transferencia" }`.
- Resultado esperado: HTTP 400 y mensaje de error.
- Estado: `no ejecutado`.

### ID: P010
- Módulo: API registrar pago con método incorrecto (GET)
- Precondiciones: cualquier entorno.
- Pasos:
  1. Enviar `GET /api/pagos/registrar.php`.
- Resultado esperado: HTTP 405, encabezado `Allow: POST`.
- Resultado obtenido: HTTP 405.
- Estado: `aprobado`.

## Casos sugeridos para Postman

- `GET /api/alumnos/perfil.php` con cookie `PHPSESSID`.
- `GET /api/adeudos/actual.php` con cookie `PHPSESSID`.
- `GET /api/adeudos/historial.php` con cookie `PHPSESSID`.
- `POST /api/pagos/registrar.php` con `metodo_pago` = `Tarjeta`.
- `POST /api/pagos/registrar.php` con `metodo_pago` = `Transferencia`.
- `GET /api/pagos/registrar.php` para validar 405.
- `GET /api/alumnos/perfil.php` sin cookies para validar 401.

## Casos de pruebas unitarias

- No se identifican pruebas unitarias implementadas en el proyecto.
- No hay archivos `phpunit.xml`, scripts de prueba ni clases de test presentes en el repositorio.
