-- Migracion idempotente: agrega el monto acumulado de periodos anteriores.
-- atraso: monto pendiente acumulado; recargo: penalizacion aplicada por atraso.
ALTER TABLE adeudos
ADD COLUMN IF NOT EXISTS atraso DECIMAL(10,2) NOT NULL DEFAULT 0.00
AFTER aportacion_tsu;
