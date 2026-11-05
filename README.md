# InventarioStock

Sistema web de control de inventario para una tienda familiar de calzado y artículos deportivos.
Registra cada artículo por **variante** (modelo + color + talla) en dos ubicaciones (**Local** y **Vivienda**),
descuenta el stock automáticamente en cada venta y guarda un historial (kardex) que no se puede borrar.

**Tecnologías:** PHP 8 (MVC propio + PDO), MySQL/MariaDB, Apache (XAMPP), HTML5, CSS3, Bootstrap 5, JavaScript y Chart.js.
No necesita internet para funcionar en la red local: Bootstrap, los íconos y Chart.js están incluidos en `assets/vendor`.

---

## 1. Instalación en XAMPP (Windows)

1. Instale **XAMPP** (versión con PHP 8.1 o superior) desde apachefriends.org.
2. Copie la carpeta `inventariostock` dentro de `C:\xampp\htdocs\`.
3. Abra el **XAMPP Control Panel** e inicie **Apache** y **MySQL**.
4. Entre a `http://localhost/phpmyadmin`, pestaña **Importar**:
   - Importe `database/inventariostock.sql` (crea la base de datos, las tablas y el usuario administrador).
   - **Catálogo real de la tienda:** importe después `database/datos_tienda.sql` (55 modelos de los documentos Word, 177 pares, con fotos por color). Impórtelo una sola vez. En `database/LEEME_catalogo.txt` está la lista de productos a completar (precios, marcas, colores agotados).
   - *Opcional, para probar o exponer:* `database/datos_demo.sql` (21 modelos de ejemplo, 2 semanas de ventas, apartados). Se puede importar junto con el catálogo real, antes que este.
5. En la carpeta del proyecto, copie el archivo `.env.example` como **`.env`** y complete los datos de MySQL (en XAMPP: usuario `root` y contraseña vacía). Este archivo guarda las credenciales y no se sube a Git.
6. Abra `http://localhost/inventariostock` en el navegador.

| Usuario | Contraseña | Rol |
|---|---|---|
| `admin` | `admin123` | Administrador (cámbiela en "Cambiar contraseña") |
| `vendedor1`, `vendedor2` | `vende123` | Vendedor (solo con los datos de demostración) |

Si MySQL tiene contraseña, escríbala en el archivo `.env` (`DB_PASS=...`). Si falta el archivo `.env`, el sistema muestra un aviso en lugar de iniciar.

## 2. Uso desde los celulares de la tienda

- **Misma red wifi:** en la PC servidor, ejecute `ipconfig` y use su dirección IPv4, por ejemplo `http://192.168.1.20/inventariostock`.
- **Desde internet (la tienda está en otro lugar):** publique el servidor con un túnel HTTPS gratuito (por ejemplo **ngrok** con dominio fijo gratuito, o **Cloudflare Tunnel**), sin abrir puertos del router. El túnel da el certificado HTTPS.
- En el celular, use "Agregar a la pantalla de inicio" para abrirlo como una aplicación.

## 3. Respaldo diario

`scripts/respaldo.bat` copia la base de datos y las fotos a una memoria USB o disco externo y borra los respaldos de más de 30 días.
Edite la línea `set DESTINO=...` con la letra de la USB y prográmelo en el **Programador de tareas** de Windows una vez al día.

Para restaurar: phpMyAdmin → Importar → el archivo `inventariostock_FECHA.sql`.

## 4. Pruebas automáticas

Con MySQL iniciado, en una ventana de comandos:

```
cd C:\xampp\htdocs\inventariostock
C:\xampp\php\php.exe tests\pruebas.php
```

Ejecuta 19 pruebas unitarias y de integración sobre una base de datos aparte (`inventariostock_pruebas`) y muestra OK / ERROR por caso.

## 5. Módulos y requisitos

| Módulo | Requisitos | Dónde está |
|---|---|---|
| M1 Seguridad y usuarios | RF01, RF02, RNF03 | `AuthController`, `UsuarioController`, `core/Auth.php` |
| M2 Catálogo (con foto principal y una foto por color) | RF03, RF04, RF05 | `ProductoController`, `CatalogoController`, `BuscarController`, `models/Producto.php` |
| M3 Reservas y encargos | RF10, RF11 | `ReservaController`, `models/Reserva.php` |
| M4 Movimientos de inventario | RF06–RF09, RF14 | `VentaController`, `InventarioController`, `models/Inventario.php` |
| M5 Alertas de stock | RF12 | `Producto::alertas()`, `reportes/alertas` |
| M6 Reportes y consultas | RF13 | `ReporteController`, `models/Reporte.php` |

**Reglas que garantiza el sistema**

- Cada operación se guarda completa o no se guarda (transacciones), y el stock nunca queda negativo: lo valida la aplicación y también la base de datos.
- Las filas de stock se bloquean durante la venta, así dos vendedores no pueden vender el mismo último par.
- Los movimientos del kardex registran usuario, fecha y hora, y no se pueden modificar ni borrar (triggers en MySQL). Las correcciones se hacen con un ajuste.
- Un par apartado deja de figurar como disponible. Al completar o anular la reserva se borran el nombre y el teléfono del cliente.
- Las fotos se guardan como archivos en `uploads/productos/` con un nombre aleatorio; la base de datos guarda solo el nombre. Cada modelo tiene una foto principal y, opcionalmente, una foto por color (se sube desde el detalle del producto).
- El vendedor no ve costos, reportes ni la administración.
- Contraseñas cifradas (`password_hash`), bloqueo temporal tras 5 intentos fallidos, cierre de sesión tras 30 minutos sin uso y protección CSRF en todos los formularios.

## 6. Estructura

```
inventariostock/
├── index.php              Punto de entrada único (index.php?r=controlador/accion)
├── .env.example           Plantilla de la conexión a MySQL (copiar como .env)
├── config/config.php      Parámetros de la aplicación; lee la conexión desde .env
├── app/
│   ├── core/              Base de datos, autenticación, controlador base, utilidades
│   ├── models/            Lógica del negocio (Inventario, Producto, Reserva, Reporte, Usuario)
│   ├── controllers/       Un controlador por sección
│   └── views/             Pantallas (HTML + PHP)
├── assets/                CSS, JavaScript, íconos y librerías
├── uploads/productos/     Fotos de los modelos
├── database/              Script de la base de datos y datos de demostración
├── scripts/respaldo.bat   Respaldo diario
└── tests/pruebas.php      Pruebas automáticas
```
