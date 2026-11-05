<?php
/**
 * Catálogo de productos y variantes (M2).
 */
final class Producto
{
    public static function ubicaciones(): array
    {
        return Database::all('SELECT id, nombre FROM ubicacion ORDER BY id');
    }

    public static function categorias(bool $soloActivas = true): array
    {
        return Database::all('SELECT * FROM categoria' . ($soloActivas ? ' WHERE activo = 1' : '') . ' ORDER BY nombre');
    }

    public static function marcas(bool $soloActivas = true): array
    {
        return Database::all('SELECT * FROM marca' . ($soloActivas ? ' WHERE activo = 1' : '') . ' ORDER BY nombre');
    }

    /**
     * Búsqueda para la atención al cliente (RF05). Devuelve productos con
     * sus variantes y el stock disponible por ubicación.
     */
    public static function buscar(array $f): array
    {
        $where  = ['p.activo = 1', 'v.activo = 1'];
        $params = [];

        if (($q = trim($f['q'] ?? '')) !== '') {
            foreach (preg_split('/\s+/', $q) as $i => $palabra) {
                $where[] = self::condicionTexto($i, $palabra, $params, ['p.nombre', 'm.nombre', 'c.nombre', 'v.color', 'v.codigo']);
            }
        }
        foreach (['categoria' => 'p.categoria_id', 'marca' => 'p.marca_id'] as $campo => $col) {
            if (!empty($f[$campo])) {
                $where[] = "$col = :$campo";
                $params[$campo] = (int) $f[$campo];
            }
        }
        if (($talla = trim($f['talla'] ?? '')) !== '') {
            $where[] = 'v.talla = :talla';
            $params['talla'] = $talla;
        }
        if (($color = trim($f['color'] ?? '')) !== '') {
            $where[] = 'v.color LIKE :color';
            $params['color'] = "%$color%";
        }

        $filas = Database::all(
            'SELECT p.id AS producto_id, p.nombre, p.foto, p.precio_venta, p.costo,
                    m.nombre AS marca, c.nombre AS categoria,
                    v.id AS variante_id, v.codigo, v.color, v.talla, v.stock_minimo, pcf.foto AS foto_color
               FROM variante v
               JOIN producto p  ON p.id = v.producto_id
               JOIN marca m     ON m.id = p.marca_id
               JOIN categoria c ON c.id = p.categoria_id
               LEFT JOIN producto_color_foto pcf ON pcf.producto_id = p.id AND pcf.color = v.color
              WHERE ' . implode(' AND ', $where) . '
              ORDER BY p.nombre, v.color, LENGTH(v.talla), v.talla
              LIMIT 600', $params);

        $existencias = self::existenciasDe(array_column($filas, 'variante_id'));
        $productos = [];
        foreach ($filas as $fila) {
            $vid = (int) $fila['variante_id'];
            $fila['stock'] = $existencias[$vid] ?? [];
            $fila['disponible_total'] = array_sum(array_column($fila['stock'], 'disponible'));
            if (!empty($f['con_stock']) && $fila['disponible_total'] <= 0) {
                continue;
            }
            $pid = $fila['producto_id'];
            $productos[$pid] ??= [
                'id' => $pid, 'nombre' => $fila['nombre'], 'foto' => $fila['foto'],
                'precio_venta' => $fila['precio_venta'], 'costo' => $fila['costo'],
                'marca' => $fila['marca'], 'categoria' => $fila['categoria'],
                'variantes' => [], 'disponible_total' => 0, 'fotos_color' => [],
            ];
            if ($fila['foto_color']) {
                $productos[$pid]['fotos_color'][$fila['color']] = $fila['foto_color'];
            }
            $productos[$pid]['variantes'][] = $fila;
            $productos[$pid]['disponible_total'] += $fila['disponible_total'];
        }
        return array_values($productos);
    }

    /**
     * Stock, reservado y disponible por variante y ubicación.
     * @return array<int, array<int, array{ubicacion:string, cantidad:int, reservado:int, disponible:int}>>
     */
    public static function existenciasDe(array $varianteIds): array
    {
        if (!$varianteIds) {
            return [];
        }
        $in = implode(',', array_map('intval', $varianteIds));
        $filas = Database::all(
            "SELECT s.variante_id, s.ubicacion_id, u.nombre AS ubicacion, s.cantidad,
                    COALESCE((SELECT SUM(r.cantidad) FROM reserva r
                               WHERE r.estado = 'ACTIVA' AND r.tipo = 'APARTADO'
                                 AND r.variante_id = s.variante_id AND r.ubicacion_id = s.ubicacion_id), 0) AS reservado
               FROM stock s JOIN ubicacion u ON u.id = s.ubicacion_id
              WHERE s.variante_id IN ($in)
              ORDER BY s.ubicacion_id");
        $res = [];
        foreach ($filas as $f) {
            $res[(int) $f['variante_id']][(int) $f['ubicacion_id']] = [
                'ubicacion'  => $f['ubicacion'],
                'cantidad'   => (int) $f['cantidad'],
                'reservado'  => (int) $f['reservado'],
                'disponible' => (int) $f['cantidad'] - (int) $f['reservado'],
            ];
        }
        return $res;
    }

    public static function obtener(int $id): ?array
    {
        $p = Database::one(
            'SELECT p.*, m.nombre AS marca, c.nombre AS categoria
               FROM producto p JOIN marca m ON m.id = p.marca_id JOIN categoria c ON c.id = p.categoria_id
              WHERE p.id = ?', [$id]);
        if (!$p) {
            return null;
        }
        $p['variantes'] = Database::all(
            'SELECT * FROM variante WHERE producto_id = ? ORDER BY activo DESC, color, LENGTH(talla), talla', [$id]);
        $exist = self::existenciasDe(array_column($p['variantes'], 'id'));
        foreach ($p['variantes'] as &$v) {
            $v['stock'] = $exist[(int) $v['id']] ?? [];
            $v['total'] = array_sum(array_column($v['stock'], 'cantidad'));
            $v['disponible_total'] = array_sum(array_column($v['stock'], 'disponible'));
        }
        unset($v);
        $p['fotos_color'] = self::fotosColor($id);
        $p['colores'] = array_values(array_unique(array_column($p['variantes'], 'color')));
        return $p;
    }

    public static function variante(int $id): ?array
    {
        $v = Database::one(
            'SELECT v.*, p.nombre, p.precio_venta, p.costo, COALESCE(pcf.foto, p.foto) AS foto, p.id AS producto_id, m.nombre AS marca
               FROM variante v JOIN producto p ON p.id = v.producto_id JOIN marca m ON m.id = p.marca_id
               LEFT JOIN producto_color_foto pcf ON pcf.producto_id = p.id AND pcf.color = v.color
              WHERE v.id = ?', [$id]);
        if ($v) {
            $v['stock'] = self::existenciasDe([$id])[$id] ?? [];
        }
        return $v;
    }

    public static function guardar(array $d, ?int $id = null): int
    {
        $params = [$d['categoria_id'], $d['marca_id'], $d['nombre'], $d['descripcion'] ?: null,
                   $d['precio_venta'], $d['costo']];
        if ($id) {
            Database::query('UPDATE producto SET categoria_id = ?, marca_id = ?, nombre = ?, descripcion = ?,
                             precio_venta = ?, costo = ? WHERE id = ?', [...$params, $id]);
            return $id;
        }
        return Database::insert('INSERT INTO producto (categoria_id, marca_id, nombre, descripcion, precio_venta, costo, creado_en)
                                 VALUES (?, ?, ?, ?, ?, ?, ?)', [...$params, ahora()]);
    }

    /**
     * Crea las variantes de todas las combinaciones color × talla.
     * Las tallas aceptan listas ("38, 39, 40") o rangos ("38-43").
     * Devuelve cuántas se crearon (omite las que ya existían).
     */
    public static function crearVariantes(int $productoId, string $colores, string $tallas, int $stockMinimo): int
    {
        $listaColores = array_values(array_unique(array_filter(array_map(
            fn($c) => mb_convert_case(trim($c), MB_CASE_TITLE), explode(',', $colores)))));
        $listaTallas = self::expandirTallas($tallas);
        if (!$listaColores || !$listaTallas) {
            throw new NegocioException('Indique al menos un color y una talla.');
        }
        $creadas = 0;
        foreach ($listaColores as $color) {
            foreach ($listaTallas as $talla) {
                $existe = Database::value('SELECT 1 FROM variante WHERE producto_id = ? AND color = ? AND talla = ?',
                    [$productoId, $color, $talla]);
                if ($existe) {
                    continue;
                }
                Database::insert('INSERT INTO variante (producto_id, codigo, color, talla, stock_minimo) VALUES (?, ?, ?, ?, ?)',
                    [$productoId, self::generarCodigo($productoId, $color, $talla), mb_substr($color, 0, 40), mb_substr($talla, 0, 10), $stockMinimo]);
                $creadas++;
            }
        }
        return $creadas;
    }

    public static function expandirTallas(string $tallas): array
    {
        $res = [];
        foreach (explode(',', $tallas) as $parte) {
            $parte = trim($parte);
            if (preg_match('/^(\d+)\s*-\s*(\d+)$/', $parte, $m) && $m[2] >= $m[1] && $m[2] - $m[1] <= 30) {
                $res = array_merge($res, array_map('strval', range((int) $m[1], (int) $m[2])));
            } elseif ($parte !== '') {
                $res[] = mb_strtoupper(mb_substr($parte, 0, 1)) . mb_substr($parte, 1);
            }
        }
        return array_values(array_unique($res));
    }

    /** Código interno legible: MARCA-PRODUCTO-COLOR-TALLA, p. ej. NIK-0007-BLA-40. */
    public static function generarCodigo(int $productoId, string $color, string $talla): string
    {
        $marca = (string) Database::value('SELECT m.nombre FROM producto p JOIN marca m ON m.id = p.marca_id WHERE p.id = ?', [$productoId]);
        $abrev = fn(string $t, int $n) => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '',
            iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $t) ?: $t), 0, $n)) ?: 'X';
        $base = sprintf('%s-%04d-%s-%s', $abrev($marca, 3), $productoId, $abrev($color, 3), $abrev($talla, 4));
        $codigo = $base;
        for ($n = 2; Database::value('SELECT 1 FROM variante WHERE codigo = ?', [$codigo]); $n++) {
            $codigo = "$base-$n";
        }
        return $codigo;
    }

    /** Guarda la foto principal del modelo. */
    public static function guardarFoto(int $productoId, array $archivo): void
    {
        $nombre = self::procesarImagen($archivo, "p$productoId");
        if ($nombre === null) {
            return;
        }
        $anterior = Database::value('SELECT foto FROM producto WHERE id = ?', [$productoId]);
        Database::query('UPDATE producto SET foto = ? WHERE id = ?', [$nombre, $productoId]);
        self::borrarArchivo($anterior);
    }

    /** Fotos por color del modelo: ['Blanco' => 'p1c_ab12.jpg', ...]. */
    public static function fotosColor(int $productoId): array
    {
        return array_column(Database::all('SELECT color, foto FROM producto_color_foto WHERE producto_id = ?', [$productoId]), 'foto', 'color');
    }

    /** Guarda (o reemplaza) la foto de un color del modelo. */
    public static function guardarFotoColor(int $productoId, string $color, array $archivo): void
    {
        if (!Database::value('SELECT 1 FROM variante WHERE producto_id = ? AND color = ?', [$productoId, $color])) {
            throw new NegocioException('Ese color no existe en el producto.');
        }
        $nombre = self::procesarImagen($archivo, "p{$productoId}c");
        if ($nombre === null) {
            throw new NegocioException('Elija una foto para el color ' . $color . '.');
        }
        $anterior = Database::value('SELECT foto FROM producto_color_foto WHERE producto_id = ? AND color = ?', [$productoId, $color]);
        Database::query('INSERT INTO producto_color_foto (producto_id, color, foto) VALUES (?, ?, ?)
                         ON DUPLICATE KEY UPDATE foto = VALUES(foto)', [$productoId, $color, $nombre]);
        self::borrarArchivo($anterior);
    }

    public static function quitarFotoColor(int $productoId, string $color): void
    {
        $anterior = Database::value('SELECT foto FROM producto_color_foto WHERE producto_id = ? AND color = ?', [$productoId, $color]);
        Database::query('DELETE FROM producto_color_foto WHERE producto_id = ? AND color = ?', [$productoId, $color]);
        self::borrarArchivo($anterior);
    }

    /**
     * Valida la imagen subida (solo JPG, PNG o WEBP, por su contenido real) y la
     * guarda en uploads/productos con un nombre aleatorio. Devuelve el nombre
     * del archivo, o null si no se eligió ninguno.
     */
    private static function procesarImagen(array $archivo, string $prefijo): ?string
    {
        if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new NegocioException('No se pudo subir la foto. Intente con una imagen más liviana.');
        }
        if ($archivo['size'] > config('app.foto_max_mb') * 1024 * 1024) {
            throw new NegocioException('La foto supera los ' . config('app.foto_max_mb') . ' MB.');
        }
        $tipos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime  = (new finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
        if (!isset($tipos[$mime]) || !@getimagesize($archivo['tmp_name'])) {
            throw new NegocioException('La foto debe ser una imagen JPG, PNG o WEBP.');
        }
        $dir = BASE_PATH . '/uploads/productos';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $nombre = sprintf('%s_%s.%s', $prefijo, bin2hex(random_bytes(6)), $tipos[$mime]);
        $movido = is_uploaded_file($archivo['tmp_name'])
            ? move_uploaded_file($archivo['tmp_name'], "$dir/$nombre")
            : (PHP_SAPI === 'cli' && copy($archivo['tmp_name'], "$dir/$nombre")); // solo para las pruebas automáticas
        if (!$movido) {
            throw new NegocioException('No se pudo guardar la foto en el servidor.');
        }
        return $nombre;
    }

    private static function borrarArchivo(?string $nombre): void
    {
        $ruta = BASE_PATH . '/uploads/productos/' . basename((string) $nombre);
        if ($nombre && !str_starts_with($nombre, 'demo_') && is_file($ruta)) {
            @unlink($ruta);
        }
    }

    /** Variantes en o por debajo de su stock mínimo (M5, RF12). */
    public static function alertas(): array
    {
        return Database::all(
            'SELECT v.id, v.codigo, v.color, v.talla, v.stock_minimo, p.id AS producto_id, p.nombre, m.nombre AS marca,
                    COALESCE(SUM(s.cantidad), 0) AS total
               FROM variante v
               JOIN producto p ON p.id = v.producto_id AND p.activo = 1
               JOIN marca m ON m.id = p.marca_id
               LEFT JOIN stock s ON s.variante_id = v.id
              WHERE v.activo = 1 AND v.stock_minimo > 0
              GROUP BY v.id, v.codigo, v.color, v.talla, v.stock_minimo, p.id, p.nombre, m.nombre
             HAVING total <= v.stock_minimo
              ORDER BY total, p.nombre, v.color, v.talla');
    }

    public static function contarAlertas(): int
    {
        return (int) Database::value(
            'SELECT COUNT(*) FROM (
                SELECT v.id FROM variante v
                  JOIN producto p ON p.id = v.producto_id AND p.activo = 1
                  LEFT JOIN stock s ON s.variante_id = v.id
                 WHERE v.activo = 1 AND v.stock_minimo > 0
                 GROUP BY v.id, v.stock_minimo
                HAVING COALESCE(SUM(s.cantidad), 0) <= v.stock_minimo) t');
    }

    /** Historial (kardex) de una variante (RF14). */
    public static function kardex(int $varianteId, int $limite = 300): array
    {
        return Database::all(
            'SELECT mv.*, u.nombre AS ubicacion, us.nombre AS usuario, o.nota
               FROM movimiento mv
               JOIN ubicacion u ON u.id = mv.ubicacion_id
               JOIN usuario us ON us.id = mv.usuario_id
               JOIN operacion o ON o.id = mv.operacion_id
              WHERE mv.variante_id = ?
              ORDER BY mv.id DESC
              LIMIT ' . (int) $limite, [$varianteId]);
    }

    /** Lista de variantes para selectores (búsqueda por texto, devuelve JSON). */
    public static function opcionesVariantes(string $q, int $limite = 20): array
    {
        $q = trim($q);
        if (mb_strlen($q) < 2) {
            return [];
        }
        $palabras = preg_split('/\s+/', $q);
        $where = ['v.activo = 1', 'p.activo = 1'];
        $params = [];
        foreach ($palabras as $i => $w) {
            $where[] = self::condicionTexto($i, $w, $params, ['p.nombre', 'm.nombre', 'v.color', 'v.codigo']);
        }
        $filas = Database::all(
            'SELECT v.id, v.codigo, v.color, v.talla, p.nombre, p.precio_venta, p.costo, m.nombre AS marca
               FROM variante v JOIN producto p ON p.id = v.producto_id JOIN marca m ON m.id = p.marca_id
              WHERE ' . implode(' AND ', $where) . '
              ORDER BY p.nombre, v.color, LENGTH(v.talla), v.talla LIMIT ' . (int) $limite, $params);
        $exist = self::existenciasDe(array_column($filas, 'id'));
        foreach ($filas as &$f) {
            $f['stock'] = array_values(array_map(fn($id, $s) => $s + ['ubicacion_id' => $id],
                array_keys($exist[(int) $f['id']] ?? []), $exist[(int) $f['id']] ?? []));
            if (!Auth::esAdmin()) {
                unset($f['costo']);
            }
        }
        return $filas;
    }

    /**
     * Condición de búsqueda de una palabra en varias columnas (o talla exacta).
     * Cada marcador es único porque MySQL no permite repetir nombres.
     */
    private static function condicionTexto(int $i, string $palabra, array &$params, array $columnas): string
    {
        $partes = [];
        foreach ($columnas as $j => $col) {
            $partes[] = "$col LIKE :q{$i}_$j";
            $params["q{$i}_$j"] = '%' . addcslashes($palabra, '%_\\') . '%';
        }
        $partes[] = "v.talla = :t$i";
        $params["t$i"] = $palabra;
        return '(' . implode(' OR ', $partes) . ')';
    }
}
