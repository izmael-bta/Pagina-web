# Pruebas de la API con Postman

Base local: `http://localhost/Portal_Web_Pagos_UTSC`

La API usa la cookie de sesión `PHPSESSID`. Para una sesión válida, inicia sesión como alumno en el navegador o ejecuta el login desde Postman y conserva las cookies.

## Perfil con sesión válida

1. Método: `GET`.
2. URL: `/api/alumnos/perfil.php`.
3. Envía la cookie válida de alumno.
4. Espera HTTP 200, `exito: true` y el objeto `alumno` sin `password_hash`.

## Solicitud sin sesión

1. Elimina las cookies de Postman.
2. Solicita cualquiera de los cuatro endpoints.
3. Espera HTTP 401 y una respuesta JSON con `exito: false`.

## Adeudo encontrado

1. Método: `GET`.
2. URL: `/api/adeudos/actual.php`.
3. Usa una sesión de alumno con adeudo.
4. Espera HTTP 200 con `periodo` y `periodo_texto`.

Para el historial usa `GET /api/adeudos/historial.php`; el arreglo debe estar ordenado del periodo más reciente al más antiguo.

## Alumno sin adeudos

Usa una sesión válida de un alumno que realmente no tenga adeudos. Tanto `actual.php` como `historial.php` deben responder HTTP 404. No inventes ni elimines registros para realizar esta prueba.

## Método HTTP incorrecto

Envía `POST` a `/api/alumnos/perfil.php` o `GET` a `/api/pagos/registrar.php`. Espera HTTP 405 y el encabezado `Allow` con el método correcto.

## Datos inválidos al registrar pago

1. Método: `POST`.
2. URL: `/api/pagos/registrar.php`.
3. Encabezado: `Content-Type: application/json`.
4. Cuerpo seguro para prueba: `{"metodo_pago":"Transferencia"}`.
5. Espera HTTP 400. Esta prueba no crea pagos ni cambia el adeudo.

También deben responder HTTP 400 un cuerpo vacío o JSON mal formado.

## Advertencia sobre la prueba válida de pago

Un POST con `Tarjeta` sobre un adeudo pendiente registra un pago real y cambia su estado a `Pagado`. Realízalo únicamente sobre datos de prueba autorizados. Las validaciones automatizadas iniciales no ejecutan este caso.
