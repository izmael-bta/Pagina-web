-- Impide duplicar el periodo mensual de un alumno.
ALTER TABLE adeudos
ADD UNIQUE INDEX IF NOT EXISTS uk_adeudo_alumno_periodo (id_alumno, periodo);
