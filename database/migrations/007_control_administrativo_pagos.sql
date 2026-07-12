ALTER TABLE pagos
 ADD COLUMN IF NOT EXISTS id_adeudo INT NULL AFTER id_alumno,
 ADD COLUMN IF NOT EXISTS estado_validacion ENUM('Pendiente','Validado','Rechazado') NOT NULL DEFAULT 'Validado' AFTER fecha_pago,
 ADD COLUMN IF NOT EXISTS origen ENUM('Portal Alumno','Manual Administrador') NOT NULL DEFAULT 'Portal Alumno' AFTER estado_validacion,
 ADD COLUMN IF NOT EXISTS registrado_por INT NULL AFTER origen,
 ADD COLUMN IF NOT EXISTS validado_por INT NULL AFTER registrado_por,
 ADD COLUMN IF NOT EXISTS fecha_validacion DATETIME NULL AFTER validado_por,
 ADD COLUMN IF NOT EXISTS observaciones VARCHAR(255) NULL AFTER fecha_validacion;

UPDATE pagos p
INNER JOIN adeudos a ON a.id_alumno=p.id_alumno AND a.estado='Pagado' AND a.total=p.total_pagado
SET p.id_adeudo=a.id_adeudo, p.estado_validacion='Validado', p.origen='Portal Alumno',
    p.fecha_validacion=p.fecha_pago, p.validado_por=NULL
WHERE p.id_adeudo IS NULL;

ALTER TABLE pagos
 ADD INDEX IF NOT EXISTS idx_pagos_id_adeudo (id_adeudo),
 ADD INDEX IF NOT EXISTS idx_pagos_registrado_por (registrado_por),
 ADD INDEX IF NOT EXISTS idx_pagos_validado_por (validado_por);

SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=DATABASE() AND TABLE_NAME='pagos' AND CONSTRAINT_NAME='fk_pagos_adeudo')=0,'ALTER TABLE pagos ADD CONSTRAINT fk_pagos_adeudo FOREIGN KEY (id_adeudo) REFERENCES adeudos(id_adeudo) ON DELETE SET NULL','SELECT 1'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=DATABASE() AND TABLE_NAME='pagos' AND CONSTRAINT_NAME='fk_pagos_registrado')=0,'ALTER TABLE pagos ADD CONSTRAINT fk_pagos_registrado FOREIGN KEY (registrado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL','SELECT 1'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
SET @sql = IF((SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA=DATABASE() AND TABLE_NAME='pagos' AND CONSTRAINT_NAME='fk_pagos_validado')=0,'ALTER TABLE pagos ADD CONSTRAINT fk_pagos_validado FOREIGN KEY (validado_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL','SELECT 1'); PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
