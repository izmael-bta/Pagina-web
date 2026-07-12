ALTER TABLE adeudos ADD COLUMN IF NOT EXISTS fecha_limite DATE NULL AFTER periodo;
UPDATE adeudos SET fecha_limite=LAST_DAY(periodo) WHERE fecha_limite IS NULL AND periodo IS NOT NULL;
