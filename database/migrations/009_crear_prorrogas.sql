CREATE TABLE IF NOT EXISTS prorrogas (
 id_prorroga INT AUTO_INCREMENT PRIMARY KEY,
 id_adeudo INT NOT NULL,
 fecha_limite_anterior DATE NOT NULL,
 nueva_fecha_limite DATE NOT NULL,
 motivo VARCHAR(255) NOT NULL,
 aplicada_por INT NULL,
 fecha_aplicacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_prorroga_adeudo FOREIGN KEY(id_adeudo) REFERENCES adeudos(id_adeudo),
 CONSTRAINT fk_prorroga_usuario FOREIGN KEY(aplicada_por) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
);
