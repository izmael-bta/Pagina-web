<?php
// Archivo de conexión central. Detecta entorno (local/producción) y crea `$conn` y `$conexion` (mysqli).
// NOTA: Este archivo está normalmente en .gitignore para evitar subir credenciales reales.

// Evitar que mysqli muestre warnings en pantalla
mysqli_report(MYSQLI_REPORT_OFF);

$host = '127.0.0.1';
$usuario = 'root';
$clave = '';
$base_datos = 'portal_pagos_utsc';

// Detectar host/entorno
$hostNombre = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
$hostNombre = strtolower($hostNombre);

if ($hostNombre === 'localhost' || $hostNombre === '127.0.0.1' || $hostNombre === '::1') {
    // Entorno local (XAMPP)
    $host = 'localhost';
    $usuario = 'root';
    $clave = '';
    $base_datos = 'portal_pagos_utsc';
} else {
    // Entorno producción (InfinityFree u otros)
    $host = 'sql312.infinityfree.com';
    $usuario = 'if0_42395681';
    $base_datos = 'if0_42395681_portal_pagos_utsc';

    // Cargar contraseña de producción desde archivo no versionado si existe
    $credsFile = __DIR__ . '/db_credentials.php';
    if (file_exists($credsFile)) {
        /**
         * El archivo `db_credentials.php` debe definir la variable:
         *   $DB_PROD_PASSWORD = 'contraseña_real';
         */
        include $credsFile;
    }

    // Valor por defecto visible para que el desarrollador lo reemplace manualmente.
    if (!isset($DB_PROD_PASSWORD) || $DB_PROD_PASSWORD === '') {
        $DB_PROD_PASSWORD = 'TU_PASSWORD_MYSQL_INFINITYFREE';
    }

    $clave = $DB_PROD_PASSWORD;
}

// Crear la conexión mysqli
$conn = new mysqli($host, $usuario, $clave, $base_datos);

// Manejo de errores: no exponer detalles al usuario, pero registrarlos en el log técnico
if ($conn->connect_error) {
    error_log('DB connection error: ' . $conn->connect_error);
    // Mensaje genérico al usuario
    die('No se pudo conectar a la base de datos. Intente nuevamente más tarde.');
}

// Establecer charset utf8mb4
if (!@$conn->set_charset('utf8mb4')) {
    error_log('No se pudo establecer charset utf8mb4 en la conexión MySQL.');
}

// Algunos ficheros del proyecto podrían esperar `$conexion` en vez de `$conn`.
$conexion = $conn;
?>
