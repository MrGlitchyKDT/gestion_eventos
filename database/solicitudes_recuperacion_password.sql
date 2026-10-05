CREATE TABLE IF NOT EXISTS solicitudes_recuperacion_password (
  id_solicitud BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_usuario BIGINT UNSIGNED NOT NULL,
  estado ENUM('PENDIENTE', 'APROBADA', 'RECHAZADA') NOT NULL DEFAULT 'PENDIENTE',
  motivo_rechazo TEXT NULL,
  id_usuario_resolucion BIGINT UNSIGNED NULL,
  fecha_solicitud TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fecha_resolucion DATETIME NULL,
  PRIMARY KEY (id_solicitud),
  KEY idx_recuperacion_estado_fecha (estado, fecha_solicitud),
  KEY idx_recuperacion_usuario (id_usuario),
  KEY idx_recuperacion_resolutor (id_usuario_resolucion),
  CONSTRAINT fk_recuperacion_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON UPDATE CASCADE,
  CONSTRAINT fk_recuperacion_resolutor FOREIGN KEY (id_usuario_resolucion) REFERENCES usuarios(id_usuario) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
