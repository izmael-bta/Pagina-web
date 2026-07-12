<?php
require_once __DIR__ . '/../../config/conexion.php';

$nombre = 'Brandon Medrano Rodríguez';
$correo = strtolower(trim('Brandon.mrodrigues@utsc.edu.mx'));
$rol = 'Administrador';
$estado = 'Activo';

$consulta = $conn->prepare('SELECT id_usuario FROM usuarios WHERE correo = ? LIMIT 1');
$consulta->bind_param('s', $correo);
$consulta->execute();

if ($consulta->get_result()->fetch_assoc() !== null) {
    echo 'El usuario administrador ya existe.' . PHP_EOL;
    exit;
}

$passwordHash = password_hash('Brandon1234@', PASSWORD_DEFAULT);

if ($passwordHash === false) {
    echo 'No fue posible crear el usuario administrador.' . PHP_EOL;
    exit(1);
}

$insercion = $conn->prepare(
    'INSERT INTO usuarios (nombre, correo, password_hash, rol, estado) VALUES (?, ?, ?, ?, ?)'
);
$insercion->bind_param('sssss', $nombre, $correo, $passwordHash, $rol, $estado);

echo $insercion->execute()
    ? 'Usuario administrador creado correctamente.' . PHP_EOL
    : 'No fue posible crear el usuario administrador.' . PHP_EOL;
