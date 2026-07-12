-- Desactivación lógica de alumnos; la migración es segura al repetirse.
ALTER TABLE alumnos
ADD COLUMN IF NOT EXISTS estado ENUM('Activo', 'Inactivo') NOT NULL DEFAULT 'Activo';
