-- =====================================================================
-- InventarioStock - CATÁLOGO REAL DE LA TIENDA (migración desde los documentos Word)
-- Zapatos de vestir, zapatillas de dama y zapatillas de varón.
-- Importar UNA SOLA VEZ, después de inventariostock.sql (con o sin datos_demo.sql).
-- Las fotos ya están en uploads/productos/ (archivos w_*.jpg).
-- Todo el stock se registra en el Local; si hay pares en la vivienda, use "Traslado".
-- =====================================================================
USE inventariostock;
SET NAMES utf8mb4;

INSERT IGNORE INTO categoria (nombre) VALUES ('Zapatillas dama'), ('Zapatillas varón'), ('Zapatos de vestir');
INSERT IGNORE INTO marca (nombre) VALUES ('Alestan'), ('Aodik'), ('Bepure'), ('Champions'), ('Cleber'), ('Conamore'), ('Full Power'), ('Grasep'), ('I-CAX'), ('I-RUN'), ('Knup'), ('Pardus'), ('Sin marca'), ('We Team');
SET @admin = (SELECT id FROM usuario WHERE rol = 'admin' ORDER BY id LIMIT 1);
INSERT INTO operacion (tipo, fecha, usuario_id, total, nota) VALUES ('INGRESO', NOW(), @admin, 0, 'Inventario inicial: migración desde los documentos Word');
SET @op = LAST_INSERT_ID();

-- Z1: Zapato de vestir 1011
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatos de vestir'), (SELECT id FROM marca WHERE nombre = 'Sin marca'), 'Zapato de vestir 1011', 'Código de modelo 1011', 'w_z1.jpg', 0.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-Z1011-CAF-38', 'Café', '38', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-Z1011-CAF-40', 'Café', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-Z1011-CAF-41', 'Café', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-Z1011-CAF-42', 'Café', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-Z1011-NEG-39', 'Negro', '39', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-Z1011-NEG-40', 'Negro', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-Z1011-NEG-41', 'Negro', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- Z2: Zapato planta de goma 1500
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatos de vestir'), (SELECT id FROM marca WHERE nombre = 'Sin marca'), 'Zapato planta de goma 1500', 'Código de modelo 1500', 'w_z2.jpg', 0.00, 0, NOW());
SET @p = LAST_INSERT_ID();

-- Z3: Zapato de vestir 199
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatos de vestir'), (SELECT id FROM marca WHERE nombre = 'Sin marca'), 'Zapato de vestir 199', 'Código de modelo 199', 'w_z3.jpg', 0.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-Z199-NEG-39', 'Negro', '39', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- D1: Zapatilla de lona
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'Sin marca'), 'Zapatilla de lona', NULL, 'w_d1.jpg', 0.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-D1-ROJ-36', 'Rojo', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Rojo', 'w_d1_rojo.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-D1-NEG-37', 'Negro', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-D1-NEG-39', 'Negro', '39', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro', 'w_d1_negro.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-D1-AZU-37', 'Azul', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-D1-AZU-38', 'Azul', '38', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_d1_azul.jpg');

-- D2: Zapatilla de lona plataforma
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'Sin marca'), 'Zapatilla de lona plataforma', NULL, 'w_d2.jpg', 0.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-D2-NEG-36', 'Negro', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-D2-NEG-37', 'Negro', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro', 'w_d2_negro.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-D2-BLA-36', 'Blanco', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Blanco', 'w_d2_blanco.jpg');

-- D3: Zapatilla de lona alta plataforma
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'Alestan'), 'Zapatilla de lona alta plataforma', NULL, 'w_d3.jpg', 0.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ALE-D3-BLA-36', 'Blanco', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ALE-D3-BLA-37', 'Blanco', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- D4: Bepure
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'Bepure'), 'Bepure', NULL, 'w_d4.jpg', 75.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'BEP-D4-AZUROS-37', 'Azul-rosado', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- D5: Knup
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'Knup'), 'Knup', NULL, 'w_d5.jpg', 70.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'KNU-D5-ROS-36', 'Rosado', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'KNU-D5-ROS-37', 'Rosado', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- D6: I-RUN dama S/110 (A)
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN dama S/110 (A)', NULL, 'w_d6.jpg', 110.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6-ROS-37', 'Rosa', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6-ROS-39', 'Rosa', '39', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Rosa', 'w_d6_rosa.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6-AZU-36', 'Azul', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6-AZU-38', 'Azul', '38', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_d6_azul.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6-GRI-38', 'Gris', '38', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Gris', 'w_d6_gris.jpg');

-- D7: I-RUN dama S/110 (B)
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN dama S/110 (B)', NULL, 'w_d7.jpg', 110.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D7-NEGROS-36', 'Negro/rosa', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro/rosa', 'w_d7_negro_rosa.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D7-AZU-36', 'Azul', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_d7_azul.jpg');

-- D8: I-RUN 5386 dama
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 5386 dama', 'Código de modelo 5386', 'w_d8.jpg', 130.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D5386-AZU-38', 'Azul', '38', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- D9: I-RUN 6015 dama
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 6015 dama', 'Código de modelo 6015', 'w_d9.jpg', 130.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6015-NEG-36', 'Negro', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6015-NEG-37', 'Negro', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro', 'w_d9_negro.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6015-PETTUR-36', 'Petróleo/planta turquesa', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6015-PETTUR-37', 'Petróleo/planta turquesa', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6015-PETTUR-38', 'Petróleo/planta turquesa', '38', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Petróleo/planta turquesa', 'w_d9_petroleo_planta_turquesa.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D6015-ROS-38', 'Rosado', '38', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Rosado', 'w_d9_rosado.jpg');

-- D10: I-RUN dama S/80
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN dama S/80', NULL, 'w_d10.jpg', 80.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D10-AZU-36', 'Azul', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D10-AZU-37', 'Azul', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_d10_azul.jpg');

-- D11: I-RUN 4517
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 4517', 'Código de modelo 4517', 'w_d11.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D4517-AZU-36', 'Azul', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D4517-AZU-37', 'Azul', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- D12: I-RUN 4158
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 4158', 'Código de modelo 4158', 'w_d12.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D4158-AZUROS-37', 'Azul/rosado', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D4158-NEG-36', 'Negro', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- D13: I-RUN 5302
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 5302', 'Código de modelo 5302', 'w_d13.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-D5302-TUR-36', 'Turquesa', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- D14: I-CAX dama S/80
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-CAX'), 'I-CAX dama S/80', NULL, 'w_d14.jpg', 80.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-D14-AZU-37', 'Azul', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- D15: I-CAX dama S/100
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-CAX'), 'I-CAX dama S/100', NULL, 'w_d15.jpg', 100.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-D15-AZU-36', 'Azul', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-D15-AZU-37', 'Azul', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_d15_azul.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-D15-NEG-37', 'Negro', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro', 'w_d15_negro.jpg');

-- D16: I-CAX 5838
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'I-CAX'), 'I-CAX 5838', 'Código de modelo 5838', 'w_d16.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-D5838-PLOTUR-37', 'Plomo/turquesa', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Plomo/turquesa', 'w_d16_plomo_turquesa.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-D5838-NEGENT-36', 'Negro entero', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro entero', 'w_d16_negro_entero.jpg');

-- D17: Conamore dama
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas dama'), (SELECT id FROM marca WHERE nombre = 'Conamore'), 'Conamore dama', NULL, 'w_d17.jpg', 60.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'CON-D17-AZUROS-37', 'Azul rosa', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'CON-D17-AZUROS-38', 'Azul rosa', '38', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'CON-D17-AZUPET-38', 'Azul petróleo', '38', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V1: I-RUN para losa S/140
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN para losa S/140', NULL, 'w_v1.jpg', 140.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V1-NEG-42', 'Negro', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V1-NEG-44', 'Negro', '44', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro', 'w_v1_negro.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V1-GRI-43', 'Gris', '43', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Gris', 'w_v1_gris.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V1-PLO-41', 'Plomo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V1-PLO-43', 'Plomo', '43', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Plomo', 'w_v1_plomo.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V1-AZU-42', 'Azul', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_v1_azul.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V1-NAR-41', 'Naranja', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V1-NAR-42', 'Naranja', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Naranja', 'w_v1_naranja.jpg');

-- V2: Grasep para losa (A)
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Grasep'), 'Grasep para losa (A)', NULL, 'w_v2.jpg', 90.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'GRA-V2-VER-45', 'Verde', '45', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Verde', 'w_v2_verde.jpg');

-- V3: Grasep para losa (B)
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Grasep'), 'Grasep para losa (B)', NULL, 'w_v3.jpg', 90.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'GRA-V3-AMA-40', 'Amarillo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Amarillo', 'w_v3_amarillo.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'GRA-V3-VER-38', 'Verde', '38', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Verde', 'w_v3_verde.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'GRA-V3-NEG-36', 'Negro', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro', 'w_v3_negro.jpg');

-- V4: Zapatilla de lona Alestan
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Alestan'), 'Zapatilla de lona Alestan', NULL, 'w_v4.jpg', 0.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ALE-V4-NEG-44', 'Negro', '44', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V5: Conamore cuero
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Conamore'), 'Conamore cuero', NULL, 'w_v5.jpg', 75.00, 0, NOW());
SET @p = LAST_INSERT_ID();

-- V6: Zapatilla de lona Alestan planta caramelo
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Alestan'), 'Zapatilla de lona Alestan planta caramelo', NULL, 'w_v6.jpg', 50.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ALE-V6-BLA-44', 'Blanco', '44', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Blanco', 'w_v6_blanco.jpg');

-- V7: Pardus
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Pardus'), 'Pardus', NULL, 'w_v7.jpg', 100.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'PAR-V7-AZU-41', 'Azul', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'PAR-V7-AZU-43', 'Azul', '43', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_v7_azul.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'PAR-V7-PET-41', 'Petróleo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'PAR-V7-PET-42', 'Petróleo', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'PAR-V7-PET-43', 'Petróleo', '43', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Petróleo', 'w_v7_petroleo.jpg');

-- V8: Knup 6775
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Knup'), 'Knup 6775', 'Código de modelo 6775', 'w_v8.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'KNU-V6775-AZU-41', 'Azul', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'KNU-V6775-PLOOSC-41', 'Plomo oscuro', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'KNU-V6775-PET-40', 'Petróleo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V9: I-RUN 6702
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 6702', 'Código de modelo 6702', 'w_v9.jpg', 130.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6702-AZUPAL-42', 'Azul/planta paloma', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul/planta paloma', 'w_v9_azul_planta_paloma.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6702-NEGCAF-43', 'Negro/planta café', '43', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6702-NEGENT-43', 'Negro entero', '43', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro entero', 'w_v9_negro_entero.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6702-PET-42', 'Petróleo', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V10: I-RUN 5479
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 5479', 'Código de modelo 5479', 'w_v10.jpg', 140.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5479-PET-39', 'Petróleo', '39', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5479-PET-40', 'Petróleo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5479-PET-42', 'Petróleo', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Petróleo', 'w_v10_petroleo.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5479-NEGVER-42', 'Negro/verde', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro/verde', 'w_v10_negro_verde.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5479-VEROSC-39', 'Verde oscuro', '39', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5479-VEROSC-40', 'Verde oscuro', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5479-VEROSC-42', 'Verde oscuro', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Verde oscuro', 'w_v10_verde_oscuro.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5479-NEGENT-40', 'Negro entero', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5479-NEGENT-41', 'Negro entero', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro entero', 'w_v10_negro_entero.jpg');

-- V11: I-RUN 5867
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 5867', 'Código de modelo 5867', 'w_v11.jpg', 140.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5867-NEGENT-40', 'Negro entero', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro entero', 'w_v11_negro_entero.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5867-AZUVER-41', 'Azul/verde', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul/verde', 'w_v11_azul_verde.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5867-AZUENT-41', 'Azul entero', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul entero', 'w_v11_azul_entero.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5867-NEGNAR-40', 'Negro/naranja', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro/naranja', 'w_v11_negro_naranja.jpg');

-- V12: I-RUN 5820
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 5820', 'Código de modelo 5820', 'w_v12.jpg', 145.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5820-NEGNAR-39', 'Negro/naranja', '39', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro/naranja', 'w_v12_negro_naranja.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5820-NEGROJ-40', 'Negro/rojo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro/rojo', 'w_v12_negro_rojo.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5820-AZUPUR-40', 'Azul puro', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5820-AZUPUR-41', 'Azul puro', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul puro', 'w_v12_azul_puro.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5820-AZUVER-40', 'Azul/verde limón', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5820-AZUVER-41', 'Azul/verde limón', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul/verde limón', 'w_v12_azul_verde_limon.jpg');

-- V13: I-RUN 6015 varón
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 6015 varón', 'Código de modelo 6015', 'w_v13.jpg', 140.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6015-NEGENT-40', 'Negro entero', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6015-NEGENT-41', 'Negro entero', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6015-PLOVER-40', 'Plomo/verde', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6015-NEGAZU-40', 'Negro/azul', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6015-NEGAZU-43', 'Negro/azul', '43', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6015-NEGNAR-42', 'Negro/naranja', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6015-AZUENT-41', 'Azul entero', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6015-NEGROJ-41', 'Negro con rojo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V6015-NEGROJ-42', 'Negro con rojo', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V14: I-RUN 5872
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 5872', 'Código de modelo 5872', 'w_v14.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5872-NEGENT-42', 'Negro entero', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro entero', 'w_v14_negro_entero.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5872-AZU-40', 'Azul', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_v14_azul.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5872-VERPET-40', 'Verde petróleo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5872-VERPET-41', 'Verde petróleo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Verde petróleo', 'w_v14_verde_petroleo.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5872-NEGAZU-40', 'Negro/azul', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5872-NEGAZU-41', 'Negro/azul', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5872-NEGAZU-42', 'Negro/azul', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro/azul', 'w_v14_negro_azul.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5872-NEGNAR-40', 'Negro/naranja', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5872-NEGNAR-41', 'Negro/naranja', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro/naranja', 'w_v14_negro_naranja.jpg');

-- V15: I-RUN 5751
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 5751', 'Código de modelo 5751', 'w_v15.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5751-AZUNAR-39', 'Azul/naranja', '39', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5751-NEGVER-40', 'Negro/verde', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5751-NEGVER-42', 'Negro/verde', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5751-NEGNAR-40', 'Negro/naranja', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V16: I-RUN varón S/130
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN varón S/130', NULL, 'w_v16.jpg', 130.00, 0, NOW());
SET @p = LAST_INSERT_ID();

-- V17: I-RUN juvenil 5161
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN juvenil 5161', 'Código de modelo 5161', 'w_v17.jpg', 100.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5161-AZU-37', 'Azul', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V18: I-RUN 4765
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 4765', 'Código de modelo 4765', 'w_v18.jpg', 100.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4765-GUI-36', 'Guinda', '36', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4765-GUI-37', 'Guinda', '37', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4765-GUI-41', 'Guinda', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Guinda', 'w_v18_guinda.jpg');

-- V19: I-RUN 5285
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 5285', 'Código de modelo 5285', 'w_v19.jpg', 110.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5285-AZUOSC-41', 'Azul oscuro', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5285-AZUOSC-42', 'Azul oscuro', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5285-NEGROJ-40', 'Negro con rojo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5285-NEGROJ-41', 'Negro con rojo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5285-NEGENT-40', 'Negro entero', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5285-AZU-41', 'Azul', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V20: I-RUN 4986
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 4986', 'Código de modelo 4986', 'w_v20.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4986-PLO-40', 'Plomo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4986-PLO-41', 'Plomo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Plomo', 'w_v20_plomo.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4986-AZU-40', 'Azul', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4986-AZU-41', 'Azul', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_v20_azul.jpg');

-- V21: I-RUN 5386 varón
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 5386 varón', 'Código de modelo 5386', 'w_v21.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5386-AZU-40', 'Azul', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5386-AZU-41', 'Azul', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_v21_azul.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5386-NEG-41', 'Negro', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Negro', 'w_v21_negro.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5386-PLO-40', 'Plomo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Plomo', 'w_v21_plomo.jpg');

-- V22: I-RUN 5493
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 5493', 'Código de modelo 5493', 'w_v22.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5493-PLOVER-40', 'Plomo/verde', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Plomo/verde', 'w_v22_plomo_verde.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5493-PLOROJ-40', 'Plomo/rojo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Plomo/rojo', 'w_v22_plomo_rojo.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5493-AZUPET-40', 'Azul petróleo/rojo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5493-AZUPET-41', 'Azul petróleo/rojo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V5493-NEGAZU-40', 'Negro/azul', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V23: I-RUN 4715
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-RUN'), 'I-RUN 4715', 'Código de modelo 4715', 'w_v23.jpg', 120.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4715-VERPAC-41', 'Verde pacae', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4715-VERPAC-45', 'Verde pacae', '45', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Verde pacae', 'w_v23_verde_pacae.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4715-AZU-42', 'Azul', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4715-AZU-43', 'Azul', '43', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Azul', 'w_v23_azul.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4715-PLO-42', 'Plomo', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'IRU-V4715-PLO-43', 'Plomo', '43', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 2);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 2, 2, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Plomo', 'w_v23_plomo.jpg');

-- V24: I-CAX 5265
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-CAX'), 'I-CAX 5265', 'Código de modelo 5265', 'w_v24.jpg', 118.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-V5265-PET-41', 'Petróleo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Petróleo', 'w_v24_petroleo.jpg');
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-V5265-PLOROJ-40', 'Plomo/rojo', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Plomo/rojo', 'w_v24_plomo_rojo.jpg');

-- V25: I-CAX Cápsula
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-CAX'), 'I-CAX Cápsula', NULL, 'w_v25.jpg', 100.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-V25-AZUOSC-40', 'Azul oscuro', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V26: I-CAX varón S/100 (A)
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-CAX'), 'I-CAX varón S/100 (A)', NULL, 'w_v26.jpg', 100.00, 0, NOW());
SET @p = LAST_INSERT_ID();

-- V27: I-CAX varón S/100 (B)
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-CAX'), 'I-CAX varón S/100 (B)', NULL, 'w_v27.jpg', 100.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-V27-PLOCLA-41', 'Plomo claro', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-V27-GRI-40', 'Gris', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V28: I-CAX varón S/100 (C)
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'I-CAX'), 'I-CAX varón S/100 (C)', NULL, 'w_v28.jpg', 100.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'ICA-V28-PLOCLA-40', 'Plomo claro', '40', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V29: We Team
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'We Team'), 'We Team', NULL, 'w_v29.jpg', 80.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'WET-V29-PLO-41', 'Plomo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Plomo', 'w_v29_plomo.jpg');

-- V30: Zapatilla de lona S/60
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Sin marca'), 'Zapatilla de lona S/60', NULL, 'w_v30.jpg', 60.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-V30-NEG-27', 'Negro', '27', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-V30-NEG-31', 'Negro', '31', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-V30-BLA-28', 'Blanco', '28', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-V30-BLA-31', 'Blanco', '31', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-V30-BLA-32', 'Blanco', '32', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-V30-AZU-42', 'Azul', '42', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'SIN-V30-ROJ-39', 'Rojo', '39', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V31: Aodik
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Aodik'), 'Aodik', NULL, 'w_v31.jpg', 80.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'AOD-V31-GUI-41', 'Guinda', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (@p, 'Guinda', 'w_v31_guinda.jpg');

-- V32: Conamore varón
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Conamore'), 'Conamore varón', NULL, 'w_v32.jpg', 75.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'CON-V32-AZU-41', 'Azul', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V33: Cleber
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Cleber'), 'Cleber', NULL, 'w_v33.jpg', 80.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'CLE-V33-PLO-41', 'Plomo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V34: Champions
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Champions'), 'Champions', NULL, 'w_v34.jpg', 75.00, 0, NOW());
SET @p = LAST_INSERT_ID();
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'CHA-V34-PLO-41', 'Plomo', '41', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);
INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (@p, 'CHA-V34-PLO-44', 'Plomo', '44', 0); SET @v = LAST_INSERT_ID();
INSERT INTO stock (variante_id, ubicacion_id, cantidad) VALUES (@v, 1, 1);
INSERT INTO movimiento (operacion_id, fecha, tipo, sentido, variante_id, ubicacion_id, cantidad, saldo, usuario_id) VALUES (@op, NOW(), 'INGRESO', 'E', @v, 1, 1, 1, @admin);

-- V35: Full Power
INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, foto, precio_venta, costo, creado_en) VALUES ((SELECT id FROM categoria WHERE nombre = 'Zapatillas varón'), (SELECT id FROM marca WHERE nombre = 'Full Power'), 'Full Power', NULL, 'w_v35.jpg', 45.00, 0, NOW());
SET @p = LAST_INSERT_ID();

