CREATE DATABASE IF NOT EXISTS portal_pagos_utsc;
USE portal_pagos_utsc;

CREATE TABLE IF NOT EXISTS alumnos (
    id_alumno INT AUTO_INCREMENT PRIMARY KEY,
    matricula VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    carrera VARCHAR(100) NOT NULL,
    grupo VARCHAR(20) NOT NULL,
    password_hash VARCHAR(255) NULL,
    estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo'
);

CREATE TABLE IF NOT EXISTS adeudos (
    id_adeudo INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT NOT NULL,
    periodo DATE NULL,
    fecha_limite DATE NULL COMMENT 'Fecha normal de vencimiento: día 15 del mes del periodo',
    mensualidad DECIMAL(10,2) NOT NULL,
    aportacion_tsu DECIMAL(10,2) NOT NULL,
    atraso DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    recargo DECIMAL(10,2) NOT NULL,
    monto_recargo_vencimiento DECIMAL(10,2) NOT NULL DEFAULT 80.00,
    total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
    FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno),
    UNIQUE KEY uk_adeudo_alumno_periodo (id_alumno, periodo)
);

CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('Administrador', 'QA') NOT NULL,
    estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo',
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS configuracion_pagos (
    id_configuracion INT AUTO_INCREMENT PRIMARY KEY,
    mensualidad DECIMAL(10,2) NOT NULL,
    aportacion_tsu DECIMAL(10,2) NOT NULL,
    recargo_vencimiento DECIMAL(10,2) NOT NULL,
    dia_limite TINYINT UNSIGNED NOT NULL,
    vigente_desde DATE NOT NULL,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    motivo_cambio VARCHAR(255) NOT NULL,
    creada_por INT NULL,
    fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_configuracion_activa (activa),
    FOREIGN KEY (creada_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

INSERT INTO configuracion_pagos
    (mensualidad, aportacion_tsu, recargo_vencimiento, dia_limite, vigente_desde, activa, motivo_cambio, creada_por)
SELECT 400.00, 50.00, 80.00, 15, '2026-07-01', 1,
       'Configuración inicial del Portal Web de Pagos UTSC', NULL
WHERE NOT EXISTS (SELECT 1 FROM configuracion_pagos);

CREATE TABLE IF NOT EXISTS pagos (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT NOT NULL,
    id_adeudo INT NULL,
    metodo_pago VARCHAR(20) NOT NULL,
    total_pagado DECIMAL(10,2) NOT NULL,
    folio VARCHAR(50) NOT NULL UNIQUE,
    fecha_pago DATETIME NOT NULL,
    estado_validacion ENUM('Pendiente','Validado','Rechazado') NOT NULL DEFAULT 'Validado',
    origen ENUM('Portal Alumno','Manual Administrador') NOT NULL DEFAULT 'Portal Alumno',
    registrado_por INT NULL,
    validado_por INT NULL,
    fecha_validacion DATETIME NULL,
    observaciones VARCHAR(255) NULL,
    FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno),
    FOREIGN KEY (id_adeudo) REFERENCES adeudos(id_adeudo) ON DELETE SET NULL,
    FOREIGN KEY (registrado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    FOREIGN KEY (validado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS prorrogas (
    id_prorroga INT AUTO_INCREMENT PRIMARY KEY,
    id_adeudo INT NOT NULL,
    fecha_limite_anterior DATE NOT NULL,
    nueva_fecha_limite DATE NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    aplicada_por INT NULL,
    fecha_aplicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_adeudo) REFERENCES adeudos(id_adeudo),
    FOREIGN KEY (aplicada_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS aclaraciones (
    id_aclaracion INT AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(50) NOT NULL UNIQUE,
    id_alumno INT NOT NULL,
    id_adeudo INT NULL,
    id_pago INT NULL,
    tipo VARCHAR(60) NOT NULL,
    asunto VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    estado ENUM('Abierta','En revisión','Resuelta','Rechazada') NOT NULL DEFAULT 'Abierta',
    respuesta TEXT NULL,
    registrada_por INT NULL,
    atendida_por INT NULL,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_atencion DATETIME NULL,
    FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno),
    FOREIGN KEY (id_adeudo) REFERENCES adeudos(id_adeudo) ON DELETE SET NULL,
    FOREIGN KEY (id_pago) REFERENCES pagos(id_pago) ON DELETE SET NULL,
    FOREIGN KEY (registrada_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL,
    FOREIGN KEY (atendida_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);

INSERT INTO alumnos (matricula, nombre, correo, carrera, grupo) VALUES
('2024001', 'Ana López García', 'ana.lopez@utsc.edu.mx', 'Desarrollo de Software Multiplataforma', 'DSM4A'),
('2024002', 'Carlos Mendoza Ruiz', 'carlos.mendoza@utsc.edu.mx', 'Desarrollo de Software Multiplataforma', 'DSM4B');

INSERT INTO adeudos (id_alumno, periodo, mensualidad, aportacion_tsu, atraso, recargo, total, estado) VALUES
  (1, '2026-07-01', 450.00, 300.00, 0.00, 80.00, 830.00, 'Pendiente'),
  (2, '2026-07-01', 450.00, 300.00, 0.00, 0.00, 750.00, 'Pendiente');
