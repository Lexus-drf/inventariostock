<?php
/**
 * Pruebas unitarias y de integración de InventarioStock.
 *
 * Uso (en la carpeta del proyecto, con MySQL iniciado):
 *     C:\xampp\php\php.exe tests\pruebas.php
 *
 * Crea una base de datos aparte, "inventariostock_pruebas", para no tocar
 * los datos reales, ejecuta cada caso y muestra OK / ERROR.
 */
$_SERVER['REQUEST_METHOD'] = 'GET';
require __DIR__ . '/../app/core/bootstrap.php';

$GLOBALS['config']['db']['name'] = 'inventariostock_pruebas';

// ---------- Preparar base de datos de pruebas ----------
$db = config('db');
$raiz = new PDO("mysql:host={$db['host']};port={$db['port']};charset=utf8mb4", $db['user'], $db['pass'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$raiz->exec('DROP DATABASE IF EXISTS inventariostock_pruebas');
$sql = file_get_contents(__DIR__ . '/../database/inventariostock.sql');
$sql = str_replace(['CREATE DATABASE IF NOT EXISTS inventariostock', 'USE inventariostock;'],
                   ['CREATE DATABASE IF NOT EXISTS inventariostock_pruebas', 'USE inventariostock_pruebas;'], $sql);
ejecutarScript($raiz, $sql);

function ejecutarScript(PDO $pdo, string $sql): void
{
    $delim = ';';
    $buf = '';
    foreach (preg_split('/\R/', $sql) as $linea) {
        if (preg_match('/^\s*DELIMITER\s+(\S+)/i', $linea, $m)) {
            $delim = $m[1];
            continue;
        }
        if ($buf === '' && preg_match('/^\s*(--|$)/', $linea)) {
            continue;
        }
        $buf .= $linea . "\n";
        if (str_ends_with(rtrim($buf), $delim)) {
            $pdo->exec(substr(rtrim($buf), 0, -strlen($delim)));
            $buf = '';
        }
    }
}

// ---------- Mini marco de pruebas ----------
$resultados = [];
function caso(string $id, string $req, string $descripcion, callable $prueba): void
{
    global $resultados;
    try {
        $prueba();
        $resultados[] = [$id, $req, $descripcion, 'OK', ''];
    } catch (Throwable $e) {
        $resultados[] = [$id, $req, $descripcion, 'ERROR', $e->getMessage()];
    }
}
function verificar(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}
function debeFallar(callable $f, string $mensaje): string
{
    try {
        $f();
    } catch (NegocioException|PDOException $e) {
        return $e->getMessage();
    }
    throw new RuntimeException($mensaje);
}
function stock(int $v, int $u): int
{
    return (int) Database::value('SELECT COALESCE(cantidad, 0) FROM stock WHERE variante_id = ? AND ubicacion_id = ?', [$v, $u]);
}

// ---------- Datos base ----------
$admin = 1;
$vend = Database::insert("INSERT INTO usuario (nombre, usuario, password_hash, rol, creado_en) VALUES ('Vendedor', 'vend', ?, 'vendedor', NOW())",
    [password_hash('clave123', PASSWORD_DEFAULT)]);
$cat = Database::insert("INSERT INTO categoria (nombre) VALUES ('Zapatillas')");
$mar = Database::insert("INSERT INTO marca (nombre) VALUES ('Nike')");
$pid = Producto::guardar(['categoria_id' => $cat, 'marca_id' => $mar, 'nombre' => 'Air Force 1', 'descripcion' => '', 'precio_venta' => 389, 'costo' => 250]);
Producto::crearVariantes($pid, 'Blanco, Negro', '38-40', 1);
$var = fn(string $color, string $talla) => (int) Database::value('SELECT id FROM variante WHERE producto_id = ? AND color = ? AND talla = ?', [$pid, $color, $talla]);
$b39 = $var('Blanco', '39');
$b40 = $var('Blanco', '40');
$n40 = $var('Negro', '40');
$LOCAL = 1;
$VIV = 2;

// ---------- Casos ----------
caso('P01', 'RF04', 'Genera una variante por color y talla con código interno único', function () use ($pid) {
    $n = (int) Database::value('SELECT COUNT(*) FROM variante WHERE producto_id = ?', [$pid]);
    verificar($n === 6, "Se esperaban 6 variantes y hay $n");
    $distintos = (int) Database::value('SELECT COUNT(DISTINCT codigo) FROM variante WHERE producto_id = ?', [$pid]);
    verificar($distintos === 6, 'Hay códigos repetidos');
    verificar(Producto::expandirTallas('38-40, M') === ['38', '39', '40', 'M'], 'No expande el rango de tallas');
});

caso('P02', 'RF06', 'El ingreso de mercadería suma el stock en la ubicación indicada', function () use ($admin, $b39, $b40, $n40, $LOCAL, $VIV) {
    Inventario::ingresar($admin, [
        ['variante_id' => $b39, 'ubicacion_id' => $LOCAL, 'cantidad' => 2, 'costo' => 250.0],
        ['variante_id' => $b40, 'ubicacion_id' => $LOCAL, 'cantidad' => 1, 'costo' => 250.0],
        ['variante_id' => $b40, 'ubicacion_id' => $VIV, 'cantidad' => 2, 'costo' => 250.0],
        ['variante_id' => $n40, 'ubicacion_id' => $LOCAL, 'cantidad' => 3, 'costo' => 250.0],
    ], 'Prueba');
    verificar(stock($b39, $LOCAL) === 2 && stock($b40, $LOCAL) === 1 && stock($b40, $VIV) === 2, 'Stock tras ingreso incorrecto');
});

caso('P03', 'RF07', 'La venta descuenta el stock automáticamente y registra el kardex', function () use ($vend, $b39, $LOCAL) {
    $op = Inventario::vender($vend, [['variante_id' => $b39, 'ubicacion_id' => $LOCAL, 'cantidad' => 1, 'precio' => 389.0]]);
    verificar(stock($b39, $LOCAL) === 1, 'El stock no bajó en uno');
    $m = Database::one('SELECT * FROM movimiento WHERE operacion_id = ?', [$op]);
    verificar($m && $m['tipo'] === 'VENTA' && (int) $m['saldo'] === 1 && (int) $m['usuario_id'] === $vend, 'Kardex incorrecto');
    verificar((float) Database::value('SELECT total FROM operacion WHERE id = ?', [$op]) === 389.0, 'Total de la venta incorrecto');
});

caso('P04', 'RNF04', 'No permite vender una talla con stock cero y avisa', function () use ($vend, $var, $LOCAL) {
    $b38 = $var('Blanco', '38');
    $msg = debeFallar(fn() => Inventario::vender($vend, [['variante_id' => $b38, 'ubicacion_id' => $LOCAL, 'cantidad' => 1, 'precio' => 389.0]]),
        'Permitió vender sin stock');
    verificar(str_contains($msg, 'no hay stock'), "Mensaje inesperado: $msg");
    verificar(stock($b38, $LOCAL) === 0, 'El stock quedó alterado');
});

caso('P05', 'RNF04', 'Si un artículo de la venta falla, no se guarda ninguno (transacción)', function () use ($vend, $b39, $n40, $LOCAL) {
    $antesOps = (int) Database::value('SELECT COUNT(*) FROM operacion');
    debeFallar(fn() => Inventario::vender($vend, [
        ['variante_id' => $n40, 'ubicacion_id' => $LOCAL, 'cantidad' => 1, 'precio' => 389.0],
        ['variante_id' => $b39, 'ubicacion_id' => $LOCAL, 'cantidad' => 5, 'precio' => 389.0],
    ]), 'Permitió una venta parcial');
    verificar(stock($n40, $LOCAL) === 3, 'Se descontó el primer artículo aunque la venta falló');
    verificar((int) Database::value('SELECT COUNT(*) FROM operacion') === $antesOps, 'Quedó una operación a medias');
});

caso('P06', 'RF08', 'El cambio de talla sube una variante, baja la otra y calcula la diferencia', function () use ($vend, $b39, $n40, $LOCAL) {
    [, $dif] = Inventario::cambiar($vend,
        ['variante_id' => $b39, 'ubicacion_id' => $LOCAL, 'cantidad' => 1, 'precio' => 389.0],
        ['variante_id' => $n40, 'ubicacion_id' => $LOCAL, 'cantidad' => 1, 'precio' => 399.0], 'Talla chica');
    verificar(stock($b39, $LOCAL) === 2 && stock($n40, $LOCAL) === 2, 'Stock del cambio incorrecto');
    verificar(abs($dif - 10.0) < 0.001, "Diferencia esperada 10.00, obtenida $dif");
});

caso('P07', 'RF09', 'Un ajuste no puede dejar el stock negativo y exige motivo', function () use ($admin, $b39, $LOCAL) {
    debeFallar(fn() => Inventario::ajustar($admin, $b39, $LOCAL, -10, 'Merma'), 'Permitió stock negativo');
    debeFallar(fn() => Inventario::ajustar($admin, $b39, $LOCAL, -1, ''), 'Permitió ajuste sin motivo');
    Inventario::ajustar($admin, $b39, $LOCAL, -1, 'Par dañado');
    verificar(stock($b39, $LOCAL) === 1, 'El ajuste válido no se aplicó');
});

caso('P08', 'RNF04', 'El traslado entre local y vivienda conserva el stock total', function () use ($vend, $b40, $LOCAL, $VIV) {
    Inventario::trasladar($vend, $b40, $VIV, $LOCAL, 2, 'Exhibición');
    verificar(stock($b40, $LOCAL) === 3 && stock($b40, $VIV) === 0, 'Traslado incorrecto');
    debeFallar(fn() => Inventario::trasladar($vend, $b40, $VIV, $LOCAL, 1), 'Trasladó desde una ubicación sin stock');
});

caso('P09', 'RF10', 'El apartado deja el par como no disponible para la venta', function () use ($vend, $b40, $LOCAL) {
    $GLOBALS['r1'] = Reserva::crear(['tipo' => 'APARTADO', 'variante_id' => $b40, 'ubicacion_id' => $LOCAL, 'descripcion' => '', 'cantidad' => 3,
        'precio' => 389.0, 'adelanto' => 100.0, 'cliente_nombre' => 'Cliente', 'cliente_telefono' => '987654321', 'fecha_limite' => null], $vend);
    verificar(Inventario::disponible($b40, $LOCAL) === 0, 'El par apartado sigue disponible');
    debeFallar(fn() => Inventario::vender($vend, [['variante_id' => $b40, 'ubicacion_id' => $LOCAL, 'cantidad' => 1, 'precio' => 389.0]]),
        'Permitió vender un par apartado');
});

caso('P10', 'RF11', 'Anular libera el stock; completar registra la venta y borra los datos del cliente', function () use ($vend, $b40, $LOCAL) {
    Reserva::anular($GLOBALS['r1']);
    verificar(Inventario::disponible($b40, $LOCAL) === 3, 'Anular no liberó el stock');
    $r2 = Reserva::crear(['tipo' => 'APARTADO', 'variante_id' => $b40, 'ubicacion_id' => $LOCAL, 'descripcion' => '', 'cantidad' => 1,
        'precio' => 389.0, 'adelanto' => 50.0, 'cliente_nombre' => 'Cliente', 'cliente_telefono' => '987654321', 'fecha_limite' => null], $vend);
    Reserva::completar($r2, $vend);
    verificar(stock($b40, $LOCAL) === 2, 'Completar no descontó el stock');
    $r = Reserva::obtener($r2);
    verificar($r['estado'] === 'COMPLETADA' && $r['cliente_nombre'] === null && $r['cliente_telefono'] === null, 'No se borraron los datos del cliente');
});

caso('P11', 'RF11', 'Un encargo sin artículo asignado no se puede completar', function () use ($vend) {
    $id = Reserva::crear(['tipo' => 'ENCARGO', 'variante_id' => 0, 'ubicacion_id' => 0, 'descripcion' => 'Air Max 90 talla 41', 'cantidad' => 1,
        'precio' => 459.0, 'adelanto' => 100.0, 'cliente_nombre' => 'Cliente', 'cliente_telefono' => '987654321', 'fecha_limite' => null], $vend);
    debeFallar(fn() => Reserva::completar($id, $vend), 'Completó un encargo sin artículo');
});

caso('P12', 'RF12', 'Aparece la alerta cuando una variante llega a su stock mínimo', function () use ($vend, $admin, $n40, $LOCAL) {
    Database::query('UPDATE variante SET stock_minimo = 1 WHERE id = ?', [$n40]);
    Inventario::vender($vend, [['variante_id' => $n40, 'ubicacion_id' => $LOCAL, 'cantidad' => 1, 'precio' => 389.0]]);
    $ids = array_map('intval', array_column(Producto::alertas(), 'id'));
    verificar(in_array($n40, $ids, true), 'La variante en su mínimo no aparece en alertas');
});

caso('P13', 'RF13', 'El reporte de ventas del día suma las ventas y los cambios registrados', function () {
    $hoy = date('Y-m-d');
    $esperado = (float) Database::value("SELECT SUM(total) FROM operacion WHERE tipo IN ('VENTA','CAMBIO') AND DATE(fecha) = ?", [$hoy]);
    $dia = Reporte::ventasPorDia($hoy, $hoy)[0];
    verificar(abs($dia['importe'] - $esperado) < 0.001 && $dia['importe'] > 0, "Importe del reporte {$dia['importe']} distinto de $esperado");
});

caso('P14', 'RNF07', 'Los movimientos del kardex no se pueden modificar ni eliminar', function () {
    debeFallar(fn() => Database::query('DELETE FROM movimiento WHERE id = 1'), 'Se pudo eliminar un movimiento');
    debeFallar(fn() => Database::query('UPDATE movimiento SET cantidad = 99 WHERE id = 1'), 'Se pudo modificar un movimiento');
});

caso('P15', 'RNF03', 'La contraseña se guarda cifrada y el inicio de sesión valida usuario y clave', function () {
    $hash = (string) Database::value("SELECT password_hash FROM usuario WHERE usuario = 'vend'");
    verificar(!str_contains($hash, 'clave123') && password_get_info($hash)['algo'] !== null, 'La contraseña no está cifrada');
    verificar(Auth::intentar('vend', 'incorrecta') !== true, 'Aceptó una contraseña incorrecta');
    verificar(Auth::intentar('vend', 'clave123') === true, 'Rechazó la contraseña correcta');
    verificar(Auth::usuario()['rol'] === 'vendedor' && !Auth::esAdmin(), 'El rol de la sesión es incorrecto');
});

caso('P16', 'RNF03', 'Bloquea la cuenta tras varios intentos fallidos', function () {
    for ($i = 0; $i < config('app.intentos_login'); $i++) {
        Auth::intentar('vend', 'mala');
    }
    $r = Auth::intentar('vend', 'clave123');
    verificar(is_string($r) && str_contains($r, 'bloqueada'), 'No bloqueó la cuenta');
    Database::query("UPDATE usuario SET bloqueado_hasta = NULL WHERE usuario = 'vend'");
});

caso('P17', 'RNF04', 'El stock en la base de datos nunca puede quedar negativo', function () use ($b39, $LOCAL) {
    debeFallar(fn() => Database::query('UPDATE stock SET cantidad = cantidad - 100 WHERE variante_id = ? AND ubicacion_id = ?', [$b39, $LOCAL]),
        'La base de datos aceptó stock negativo');
});

caso('P18', 'RNF07', 'El saldo del kardex coincide con el stock real de cada variante', function () {
    $filas = Database::all('SELECT v.id, COALESCE((SELECT SUM(cantidad) FROM stock s WHERE s.variante_id = v.id), 0) AS stock,
                                   (SELECT saldo FROM movimiento m WHERE m.variante_id = v.id ORDER BY m.id DESC LIMIT 1) AS saldo
                              FROM variante v');
    foreach ($filas as $f) {
        if ($f['saldo'] !== null) {
            verificar((int) $f['saldo'] === (int) $f['stock'], "Variante {$f['id']}: saldo {$f['saldo']} ≠ stock {$f['stock']}");
        }
    }
});

caso('P19', 'RF03', 'Guarda una foto por color: el archivo va al disco y la base guarda solo su nombre', function () use ($pid, $b40) {
    $tmp = tempnam(sys_get_temp_dir(), 'img');
    $im = imagecreatetruecolor(40, 40);
    imagejpeg($im, $tmp);
    Producto::guardarFotoColor($pid, 'Blanco', ['error' => UPLOAD_ERR_OK, 'size' => filesize($tmp), 'tmp_name' => $tmp]);
    $foto = Producto::fotosColor($pid)['Blanco'] ?? null;
    verificar($foto && is_file(BASE_PATH . '/uploads/productos/' . $foto), 'No se guardó el archivo de la foto');
    verificar(Producto::variante($b40)['foto'] === $foto, 'La variante blanca no muestra la foto de su color');
    $falso = tempnam(sys_get_temp_dir(), 'txt');
    file_put_contents($falso, '<?php echo 1;');
    debeFallar(fn() => Producto::guardarFotoColor($pid, 'Negro', ['error' => UPLOAD_ERR_OK, 'size' => 13, 'tmp_name' => $falso]), 'Aceptó un archivo que no es imagen');
    Producto::quitarFotoColor($pid, 'Blanco');
    verificar(!is_file(BASE_PATH . '/uploads/productos/' . $foto) && !Producto::fotosColor($pid), 'No se quitó la foto');
});

// ---------- Resultados ----------
$raiz->exec('DROP DATABASE IF EXISTS inventariostock_pruebas');
$ok = count(array_filter($resultados, fn($r) => $r[3] === 'OK'));
echo "\nInventarioStock · Pruebas unitarias y de integración · " . date('d/m/Y H:i') . "\n";
echo str_repeat('=', 100) . "\n";
foreach ($resultados as [$id, $req, $desc, $estado, $error]) {
    printf("%-4s %-6s %-6s %s\n", $id, $req, $estado, $desc);
    if ($error) {
        echo "            → $error\n";
    }
}
echo str_repeat('=', 100) . "\n";
printf("Total: %d · Exitosas: %d · Fallidas: %d · Éxito: %.0f %%\n\n", count($resultados), $ok, count($resultados) - $ok, 100 * $ok / count($resultados));
exit($ok === count($resultados) ? 0 : 1);
