# API

## Existencia real de una API

Sí, el proyecto incluye una API local basada en PHP y sesiones.

## Endpoints encontrados

### `GET /api/alumnos/perfil.php`
- Resumen: obtiene el perfil del alumno autenticado.
- Parámetros: ninguno.
- Cuerpo JSON: no aplica.
- Respuesta 200:
  - `exito`: true
  - `alumno`: objeto con `id_alumno`, `matricula`, `nombre`, `correo`, `carrera`, `grupo`
- Códigos HTTP:
  - 401: sesión inválida o ausente.
  - 404: perfil no encontrado.
  - 405: método incorrecto.
  - 500: error interno.
- Validaciones:
  - método `GET` requerido.
  - sesión válida con `id_alumno` y `rol = alumno`.

### `GET /api/adeudos/actual.php`
- Resumen: obtiene el adeudo más reciente del alumno autenticado.
- Parámetros: ninguno.
- Cuerpo JSON: no aplica.
- Respuesta 200:
  - `exito`: true
  - `adeudo`: objeto con `id_adeudo`, `periodo`, `periodo_texto`, `mensualidad`, `aportacion_tsu`, `atraso`, `recargo`, `total`, `estado`
- Códigos HTTP:
  - 401: sesión inválida o ausente.
  - 404: no se encontraron adeudos.
  - 405: método incorrecto.
  - 500: error interno.
- Validaciones:
  - método `GET` requerido.
  - sesión válida.

### `GET /api/adeudos/historial.php`
- Resumen: obtiene el historial completo de adeudos del alumno autenticado.
- Parámetros: ninguno.
- Cuerpo JSON: no aplica.
- Respuesta 200:
  - `exito`: true
  - `adeudos`: arreglo de objetos `Adeudo`
- Códigos HTTP:
  - 401: sesión inválida o ausente.
  - 404: no se encontraron adeudos.
  - 405: método incorrecto.
  - 500: error interno.
- Validaciones:
  - método `GET` requerido.
  - sesión válida.

### `POST /api/pagos/registrar.php`
- Resumen: registra el pago del adeudo actual del alumno autenticado.
- Parámetros: ninguno en URL.
- Cuerpo JSON:
  - `metodo_pago` (string): requerido. Valores válidos: `Tarjeta`, `Efectivo`.
- Respuesta 201:
  - `exito`: true
  - `pago`: objeto con `id_adeudo`, `metodo_pago`, `total_pagado`, `folio`, `fecha_pago`
- Códigos HTTP:
  - 400: método de pago inválido, cuerpo JSON vacío o adeudo ya pagado.
  - 401: sesión inválida o ausente.
  - 404: no se encontró adeudo.
  - 405: método incorrecto.
  - 500: error interno.
- Validaciones:
  - método `POST` requerido.
  - cuerpo JSON válido y con `metodo_pago` correcto.
  - sesión válida.
  - adeudo existente y no pagado.

## Cuerpo JSON

- `api/pagos/registrar.php` requiere:
  - `metodo_pago` = `Tarjeta` o `Efectivo`
- Los demás datos del pago se obtienen en el servidor:
  - `id_alumno` desde la sesión.
  - `total_pagado` desde el adeudo actual en la base de datos.

## Respuestas

- Éxito:
  - `{'exito': true, 'alumno': {...}}`
  - `{'exito': true, 'adeudo': {...}}`
  - `{'exito': true, 'adeudos': [...]}`
  - `{'exito': true, 'pago': {...}}`
- Error:
  - `{'exito': false, 'mensaje': '...'}`

## Ejemplos de solicitudes

### Perfil

```
GET http://localhost/Portal_Web_Pagos_UTSC/api/alumnos/perfil.php
Cookie: PHPSESSID=<sesion_valida>
```

### Adeudo actual

```
GET http://localhost/Portal_Web_Pagos_UTSC/api/adeudos/actual.php
Cookie: PHPSESSID=<sesion_valida>
```

### Historial

```
GET http://localhost/Portal_Web_Pagos_UTSC/api/adeudos/historial.php
Cookie: PHPSESSID=<sesion_valida>
```

### Registrar pago

```
POST http://localhost/Portal_Web_Pagos_UTSC/api/pagos/registrar.php
Content-Type: application/json
Cookie: PHPSESSID=<sesion_valida>

{
  "metodo_pago": "Tarjeta"
}
```

## Endpoint faltante

- No existe endpoint para el login o autenticación desde la API.
- No existe endpoint para cerrar sesión desde la API.
- No existe endpoint para administrar alumnos, adeudos o pagos.
- No existe endpoint para que `login-admin.php` o `login-qa.php` funcionen.

## Propuestas

Se recomienda proponer:

- `POST /api/auth/login` para autenticación.
- `POST /api/auth/logout` para cierre de sesión.
- `GET /api/pagos/historial.php` separado para pagos realizados.
- `GET /api/alumnos/{id}` para consulta de alumno por ID (administración).
- `POST /api/adeudos` / `PUT /api/adeudos/{id}` / `DELETE /api/adeudos/{id}` para CRUD.
