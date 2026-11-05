-- =====================================================================
--  InventarioStock - Sistema web de control de inventario
--  Base de datos MySQL / MariaDB (XAMPP)
--
--  Importar en phpMyAdmin: pestaña "Importar" -> elegir este archivo.
--  Crea la base de datos "inventariostock", sus tablas, las reglas que
--  protegen el historial y los datos mínimos (ubicaciones y administrador).
-- =====================================================================

CREATE DATABASE IF NOT EXISTS inventariostock
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventariostock;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS movimiento;
DROP TABLE IF EXISTS reserva;
DROP TABLE IF EXISTS operacion;
DROP TABLE IF EXISTS stock;
DROP TABLE IF EXISTS producto_color_foto;
DROP TABLE IF EXISTS variante;
DROP TABLE IF EXISTS producto;
DROP TABLE IF EXISTS marca;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS ubicacion;
DROP TABLE IF EXISTS usuario;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- M1. Seguridad y usuarios
-- ---------------------------------------------------------------------
CREATE TABLE usuario (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre            VARCHAR(100) NOT NULL,
  usuario           VARCHAR(40)  NOT NULL UNIQUE,
  password_hash     VARCHAR(255) NOT NULL,
  rol               ENUM('admin','vendedor') NOT NULL DEFAULT 'vendedor',
  activo            TINYINT(1) NOT NULL DEFAULT 1,
  intentos_fallidos TINYINT UNSIGNED NOT NULL DEFAULT 0,
  bloqueado_hasta   DATETIME NULL,
  ultimo_acceso     DATETIME NULL,
  creado_en         DATETIME NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- M2. Catálogo
-- ---------------------------------------------------------------------
CREATE TABLE categoria (
  id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre  VARCHAR(60) NOT NULL UNIQUE,
  activo  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE marca (
  id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre  VARCHAR(60) NOT NULL UNIQUE,
  activo  TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE producto (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  categoria_id  INT UNSIGNED NOT NULL,
  marca_id      INT UNSIGNED NOT NULL,
  nombre        VARCHAR(120) NOT NULL,              -- nombre del modelo
  descripcion   VARCHAR(255) NULL,
  foto          VARCHAR(255) NULL,                  -- ruta del archivo en uploads/
  precio_venta  DECIMAL(10,2) NOT NULL,
  costo         DECIMAL(10,2) NOT NULL DEFAULT 0,   -- visible solo para el administrador
  activo        TINYINT(1) NOT NULL DEFAULT 1,
  creado_en     DATETIME NOT NULL,
  CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id) REFERENCES categoria(id),
  CONSTRAINT fk_producto_marca     FOREIGN KEY (marca_id)     REFERENCES marca(id),
  CONSTRAINT chk_producto_precio   CHECK (precio_venta >= 0 AND costo >= 0)
) ENGINE=InnoDB;

-- La unidad de inventario es la variante: modelo + color + talla.
CREATE TABLE variante (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  producto_id   INT UNSIGNED NOT NULL,
  codigo        VARCHAR(30) NOT NULL UNIQUE,         -- código interno único
  color         VARCHAR(40) NOT NULL,
  talla         VARCHAR(10) NOT NULL,                -- "40", "M", "Única"...
  stock_minimo  INT UNSIGNED NOT NULL DEFAULT 1,
  activo        TINYINT(1) NOT NULL DEFAULT 1,
  CONSTRAINT fk_variante_producto FOREIGN KEY (producto_id) REFERENCES producto(id),
  CONSTRAINT uq_variante UNIQUE (producto_id, color, talla)
) ENGINE=InnoDB;

-- Foto de cada color de un modelo (opcional). El archivo se guarda en
-- uploads/productos/ y aquí solo su nombre. Si un color no tiene foto,
-- se muestra la foto principal del producto.
CREATE TABLE producto_color_foto (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  producto_id  INT UNSIGNED NOT NULL,
  color        VARCHAR(40) NOT NULL,
  foto         VARCHAR(255) NOT NULL,
  CONSTRAINT fk_pcf_producto FOREIGN KEY (producto_id) REFERENCES producto(id),
  CONSTRAINT uq_pcf UNIQUE (producto_id, color)
) ENGINE=InnoDB;

CREATE TABLE ubicacion (
  id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre  VARCHAR(40) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Existencia de cada variante en cada ubicación. Nunca puede ser negativa.
CREATE TABLE stock (
  variante_id   INT UNSIGNED NOT NULL,
  ubicacion_id  INT UNSIGNED NOT NULL,
  cantidad      INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (variante_id, ubicacion_id),
  CONSTRAINT fk_stock_variante  FOREIGN KEY (variante_id)  REFERENCES variante(id),
  CONSTRAINT fk_stock_ubicacion FOREIGN KEY (ubicacion_id) REFERENCES ubicacion(id),
  CONSTRAINT chk_stock_cantidad CHECK (cantidad >= 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- M4. Movimientos de inventario
-- Una operación agrupa los movimientos registrados juntos (una venta de
-- varios pares, un cambio de talla, un ingreso de mercadería...).
-- ---------------------------------------------------------------------
CREATE TABLE operacion (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tipo        ENUM('VENTA','INGRESO','CAMBIO','AJUSTE','TRASLADO') NOT NULL,
  fecha       DATETIME NOT NULL,
  usuario_id  INT UNSIGNED NOT NULL,
  total       DECIMAL(10,2) NOT NULL DEFAULT 0,  -- venta: importe; cambio: diferencia cobrada (+) o devuelta (-)
  nota        VARCHAR(255) NULL,
  CONSTRAINT fk_operacion_usuario FOREIGN KEY (usuario_id) REFERENCES usuario(id),
  INDEX idx_operacion_fecha (tipo, fecha)
) ENGINE=InnoDB;

-- Kardex: cada fila es una entrada o salida de una variante en una ubicación.
CREATE TABLE movimiento (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  operacion_id    INT UNSIGNED NOT NULL,
  fecha           DATETIME NOT NULL,
  tipo            ENUM('INGRESO','VENTA','DEVOLUCION_CAMBIO','SALIDA_CAMBIO',
                       'AJUSTE_ENTRADA','AJUSTE_SALIDA','TRASLADO_SALIDA','TRASLADO_ENTRADA') NOT NULL,
  sentido         ENUM('E','S') NOT NULL,              -- E = entrada, S = salida
  variante_id     INT UNSIGNED NOT NULL,
  ubicacion_id    INT UNSIGNED NOT NULL,
  cantidad        INT UNSIGNED NOT NULL,
  saldo           INT UNSIGNED NOT NULL,               -- stock total de la variante después del movimiento
  precio_unitario DECIMAL(10,2) NULL,                  -- precio de venta aplicado
  costo_unitario  DECIMAL(10,2) NULL,                  -- costo de compra (ingresos)
  motivo          VARCHAR(255) NULL,
  usuario_id      INT UNSIGNED NOT NULL,
  CONSTRAINT fk_mov_operacion FOREIGN KEY (operacion_id) REFERENCES operacion(id),
  CONSTRAINT fk_mov_variante  FOREIGN KEY (variante_id)  REFERENCES variante(id),
  CONSTRAINT fk_mov_ubicacion FOREIGN KEY (ubicacion_id) REFERENCES ubicacion(id),
  CONSTRAINT fk_mov_usuario   FOREIGN KEY (usuario_id)   REFERENCES usuario(id),
  CONSTRAINT chk_mov_cantidad CHECK (cantidad > 0),
  INDEX idx_mov_variante (variante_id, id),
  INDEX idx_mov_fecha (fecha)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- M3. Reservas: apartados (separan stock existente) y encargos
-- (pedidos de mercadería que aún no hay). Los datos del cliente se
-- borran al completar o anular la reserva.
-- ---------------------------------------------------------------------
CREATE TABLE reserva (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tipo              ENUM('APARTADO','ENCARGO') NOT NULL,
  variante_id       INT UNSIGNED NULL,
  ubicacion_id      INT UNSIGNED NULL,
  descripcion       VARCHAR(255) NULL,          -- para encargos de modelos que no están en el catálogo
  cantidad          INT UNSIGNED NOT NULL DEFAULT 1,
  precio            DECIMAL(10,2) NOT NULL,     -- precio unitario acordado
  adelanto          DECIMAL(10,2) NOT NULL DEFAULT 0,
  cliente_nombre    VARCHAR(100) NULL,
  cliente_telefono  VARCHAR(20) NULL,
  estado            ENUM('ACTIVA','COMPLETADA','ANULADA') NOT NULL DEFAULT 'ACTIVA',
  fecha             DATETIME NOT NULL,
  fecha_limite      DATE NULL,
  usuario_id        INT UNSIGNED NOT NULL,
  cerrado_en        DATETIME NULL,
  operacion_id      INT UNSIGNED NULL,          -- venta generada al completarla
  CONSTRAINT fk_reserva_variante  FOREIGN KEY (variante_id)  REFERENCES variante(id),
  CONSTRAINT fk_reserva_ubicacion FOREIGN KEY (ubicacion_id) REFERENCES ubicacion(id),
  CONSTRAINT fk_reserva_usuario   FOREIGN KEY (usuario_id)   REFERENCES usuario(id),
  CONSTRAINT fk_reserva_operacion FOREIGN KEY (operacion_id) REFERENCES operacion(id),
  CONSTRAINT chk_reserva CHECK (cantidad > 0 AND adelanto >= 0 AND precio >= 0),
  INDEX idx_reserva_estado (estado, variante_id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Reglas de trazabilidad (RNF07): el historial no se modifica ni se borra.
-- Las correcciones se hacen con un ajuste, que también queda registrado.
-- ---------------------------------------------------------------------
DELIMITER $$
CREATE TRIGGER trg_movimiento_no_update BEFORE UPDATE ON movimiento
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Los movimientos no se pueden modificar; registre un ajuste.';
END$$

CREATE TRIGGER trg_movimiento_no_delete BEFORE DELETE ON movimiento
FOR EACH ROW
BEGIN
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Los movimientos no se pueden eliminar; registre un ajuste.';
END$$
DELIMITER ;

-- ---------------------------------------------------------------------
-- Datos mínimos
-- ---------------------------------------------------------------------
INSERT INTO ubicacion (id, nombre) VALUES (1, 'Local'), (2, 'Vivienda');

-- Administrador inicial. Usuario: admin  ·  Contraseña: admin123
-- (cámbiela al primer ingreso desde "Mi cuenta")
INSERT INTO usuario (nombre, usuario, password_hash, rol, creado_en)
VALUES ('Administrador', 'admin', '$2y$10$n4H52iSSTXw0cS4qsv6hq.wSnxRGTVxyhas.weltz/jN3HiPvXjSO', 'admin', NOW());
