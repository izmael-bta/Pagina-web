# Portal Web de Pagos UTSC

Proyecto escolar local organizado con una arquitectura MVC básica, sin frameworks ni dependencias externas.

## Requisitos

- XAMPP con Apache, PHP y MySQL.
- Base de datos `portal_pagos_utsc` importada desde `database/database.sql`.

## Ejecución

Coloca el proyecto en `C:\xampp\htdocs\Portal_Web_Pagos_UTSC`, inicia Apache y MySQL, y visita:

`http://localhost/Portal_Web_Pagos_UTSC/index.php?ruta=login`

## Rutas

- `ruta=login`: ingreso por matrícula.
- `ruta=autenticar-alumno`: procesa el acceso del alumno mediante POST.
- `ruta=crear-password`: activación inicial de contraseña del alumno.
- `ruta=guardar-password`: guarda la contraseña inicial mediante POST.
- `ruta=login-admin`: interfaz de acceso del administrador.
- `ruta=login-qa`: interfaz de acceso de Control de Calidad.
- `ruta=registro`: actividad de registro del estudiante.
- `ruta=alumno`: datos y adeudos del alumno autenticado.
- `ruta=pago`: formulario de pago.
- `ruta=comprobante`: comprobante del pago.
- `ruta=salir`: cierre de sesión.

## Estructura

- `app/controllers`: sesiones, validaciones, redirecciones y flujo.
- `app/models`: consultas y operaciones de base de datos.
- `app/views`: HTML de las pantallas.
- `config`: conexión MySQL.
- `public`: CSS, JavaScript e imágenes.
- `database`: esquema y datos iniciales.

## Migraciones

Para una base existente, ejecuta una sola vez las migraciones necesarias en orden:

1. `database/migrations/001_agregar_password_alumnos.sql`.
2. `database/migrations/002_agregar_periodo_adeudos.sql`.
3. `database/migrations/003_asignar_periodo_julio_2026_adeudos_existentes.sql`, únicamente si se confirmó que los adeudos sin periodo corresponden a julio de 2026.

La instalación completa de `database/database.sql` ya incluye las columnas `password_hash` y `periodo`.

## API local

Los endpoints JSON autenticados por la sesión PHP están disponibles en `api/`. La especificación completa se encuentra en `docs/openapi.yaml` y los casos manuales en `docs/pruebas-postman.md`.
