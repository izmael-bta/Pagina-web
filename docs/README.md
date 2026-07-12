# Portal Web de Pagos UTSC

Documentación técnica generada a partir del código existente del proyecto sin modificaciones.

## Descripción general

Portal Web de Pagos UTSC es una aplicación PHP local para la consulta de adeudos escolares y la simulación de pagos por parte de alumnos. Está pensada para ejecutarse en XAMPP con Apache, PHP y MySQL.

## Objetivo

Permitir a los alumnos:

- Iniciar sesión con matrícula y contraseña.
- Crear una contraseña inicial a partir de matrícula y correo registrado.
- Consultar sus datos personales y el adeudo más reciente.
- Simular el registro de un pago y visualizar un comprobante.
- Acceder a una API local autenticada con sesión para obtener perfil, adeudo actual e historial.

## Tecnologías utilizadas

- PHP 8+ (sintaxis de tipado y MySQLi)
- HTML5
- CSS3
- JavaScript (DOM, validaciones simples y generación de contraseña segura)
- MySQL
- XAMPP (Apache + MySQL)

## Requisitos de instalación

1. Instalar XAMPP en Windows.
2. Copiar el proyecto a `C:\xampp\htdocs\Portal_Web_Pagos_UTSC`.
3. Importar la base de datos desde `database/database.sql`.
4. Iniciar Apache y MySQL en el Panel de Control de XAMPP.

## Instrucciones para ejecutar en XAMPP

1. Abrir el Panel de Control de XAMPP.
2. Iniciar los servicios de Apache y MySQL.
3. Colocar el proyecto en `C:\xampp\htdocs\Portal_Web_Pagos_UTSC`.
4. Importar `database/database.sql` usando phpMyAdmin o la línea de comandos.
5. Abrir el navegador en:

   `http://localhost/Portal_Web_Pagos_UTSC/index.php?ruta=login`

## Nombre de la base de datos

- `portal_pagos_utsc`

## Usuarios de prueba

La base de datos incluye los alumnos registrados en la tabla `alumnos`.

- Matrículas disponibles: `25918`, `25906`, `25987`, `26017`, `25735`, `25766`, `25639`, `25646`, `25834`, `25653`, `25836`, `25658`, `25934`, `25786`, `25894`, `25927`, `25994`, `25699`.
- Matrícula con contraseña ya creada: `25906` (el valor del hash está presente en la base de datos, pero la contraseña no se expone en el código ni en este documento).
- Los demás alumnos requieren crear su contraseña inicial mediante `index.php?ruta=crear-password` usando matrícula y correo registrados.

## Estructura de carpetas

- `index.php`: front controller y enrutador principal.
- `config/conexion.php`: configuración de conexión MySQL.
- `app/controllers/`: controladores de flujo de aplicación.
- `app/models/`: modelos de acceso a datos.
- `app/views/`: vistas HTML de usuario.
- `app/helpers/`: utilidades compartidas.
- `api/`: endpoints JSON autenticados por sesión.
- `public/css/`: estilos.
- `public/js/`: scripts de interfaz.
- `database/`: esquema SQL y migraciones.
- `docs/`: documentación técnica del proyecto.
