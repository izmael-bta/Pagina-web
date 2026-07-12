-- Backfill separado: ejecutar solo después de confirmar que estos adeudos corresponden a julio de 2026.
UPDATE adeudos
SET periodo = '2026-07-01'
WHERE periodo IS NULL;
