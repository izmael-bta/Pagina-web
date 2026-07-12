USE portal_pagos_utsc;

ALTER TABLE adeudos
    ADD COLUMN IF NOT EXISTS monto_recargo_vencimiento DECIMAL(10,2) NOT NULL DEFAULT 80.00
    AFTER recargo;

UPDATE adeudos
SET monto_recargo_vencimiento = 80.00
WHERE monto_recargo_vencimiento IS NULL;
