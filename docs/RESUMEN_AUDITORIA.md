# Resumen Ejecutivo de Auditoría

## Objetivo

Auditar el proyecto "Portal Web de Pagos UTSC" basado exclusivamente en el código y archivos del repositorio.

## Hallazgos principales

- El proyecto es una aplicación PHP local que funciona con XAMPP y MySQL.
- Se implementa una arquitectura aproximada a MVC con `index.php` como front controller.
- Las funcionalidades centrales de alumno están presentes: login, creación de contraseña inicial, consulta de adeudos, pago simulado y comprobante.
- Existe una API local autenticada por sesión con endpoints reales para perfil, adeudo actual, historial y registro de pago.
- Las rutas de administrador y QA (`login-admin` y `login-qa`) solo tienen interfaz, no lógica de backend.

## Estado general

- Avance estimado: 70%.
- El núcleo funcional del alumno está implementado.
- Faltan funcionalidades administrativas, historial UI, CRUD completo y mejoras de seguridad.

## Riesgos detectados

- Falta de protección CSRF en formularios.
- Cookies de sesión sin configuración explícita `Secure`, `SameSite` o `HttpOnly`.
- Rutas de administrador y QA expuestas sin backend seguro.
- Relación entre `pagos` y `adeudos` no normalizada en la base de datos.
- Dependencia de la sesión PHP para la API, sin tokens o separación de autenticación.

## Recomendaciones críticas

1. Implementar CSRF en formularios de estado.
2. Ajustar los parámetros de cookie de sesión para entornos HTTPS.
3. Añadir lógica real o remover las pantallas de administrador y QA.
4. Relacionar `pagos` con `adeudos` usando `id_adeudo`.
5. Agregar vistas de historial de adeudos y CRUD administrativo.

## Documentos generados

- `docs/README.md`
- `docs/ESTADO_PROYECTO.md`
- `docs/ARQUITECTURA.md`
- `docs/BASE_DATOS.md`
- `docs/FUNCIONALIDADES.md`
- `docs/API.md`
- `docs/SEGURIDAD.md`
- `docs/PRUEBAS.md`
- `docs/REQUERIMIENTOS.md`
- `docs/INVENTARIO_ARCHIVOS.md`

## Conclusión

El proyecto tiene una base sólida para un portal de pagos de alumnos, con una implementación consistente de la experiencia de alumno. Sin embargo, requiere finalización de roles adicionales, mejoras de seguridad y normalización de la base de datos para ser considerado completo y listo para producción.
