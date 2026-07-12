-- Corrige únicamente fechas automáticas anteriores, sin tocar adeudos prorrogados.
UPDATE adeudos a
LEFT JOIN prorrogas p ON p.id_adeudo = a.id_adeudo
SET a.fecha_limite = STR_TO_DATE(
    CONCAT(YEAR(a.periodo), '-', LPAD(MONTH(a.periodo), 2, '0'), '-15'),
    '%Y-%m-%d'
)
WHERE p.id_prorroga IS NULL
  AND a.periodo IS NOT NULL
  AND (a.fecha_limite IS NULL OR a.fecha_limite = LAST_DAY(a.periodo));
