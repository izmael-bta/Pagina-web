USE portal_pagos_utsc;

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
    CONSTRAINT fk_configuracion_usuario FOREIGN KEY (creada_por)
        REFERENCES usuarios(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO configuracion_pagos
    (mensualidad, aportacion_tsu, recargo_vencimiento, dia_limite, vigente_desde, activa, motivo_cambio, creada_por)
SELECT 400.00, 50.00, 80.00, 15, '2026-07-01', 1,
       'Configuración inicial del Portal Web de Pagos UTSC', NULL
WHERE NOT EXISTS (SELECT 1 FROM configuracion_pagos);
