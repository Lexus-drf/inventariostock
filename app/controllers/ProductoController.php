<?php
/**
 * Productos y variantes (RF03, RF04) y kardex (RF14).
 * Ver el detalle y el kardex: todos. Crear y editar: solo administrador.
 */
class ProductoController extends Controller
{
    private const SOLO_ADMIN = ['index', 'crear', 'editar', 'guardar', 'variantes', 'variante', 'activar', 'fotoColor', 'quitarFotoColor'];

    public function antesDe(string $accion): void
    {
        parent::antesDe($accion);
        if (in_array($accion, self::SOLO_ADMIN, true)) {
            $this->soloAdmin();
        }
    }

    public function index(): void
    {
        $f = ['q' => texto($_GET['q'] ?? '', 80), 'categoria' => entero($_GET['categoria'] ?? 0), 'inactivos' => !empty($_GET['inactivos'])];
        $where = [$f['inactivos'] ? '1 = 1' : 'p.activo = 1'];
        $params = [];
        if ($f['q'] !== '') {
            $where[] = '(p.nombre LIKE ? OR m.nombre LIKE ?)';
            array_push($params, "%{$f['q']}%", "%{$f['q']}%");
        }
        if ($f['categoria']) {
            $where[] = 'p.categoria_id = ?';
            $params[] = $f['categoria'];
        }
        $productos = Database::all(
            'SELECT p.*, m.nombre AS marca, c.nombre AS categoria,
                    (SELECT COUNT(*) FROM variante v WHERE v.producto_id = p.id AND v.activo = 1) AS n_variantes,
                    (SELECT COALESCE(SUM(s.cantidad), 0) FROM stock s JOIN variante v ON v.id = s.variante_id WHERE v.producto_id = p.id) AS stock
               FROM producto p JOIN marca m ON m.id = p.marca_id JOIN categoria c ON c.id = p.categoria_id
              WHERE ' . implode(' AND ', $where) . ' ORDER BY p.activo DESC, c.nombre, p.nombre', $params);
        $this->render('productos/index', ['productos' => $productos, 'f' => $f, 'categorias' => Producto::categorias()], 'Productos');
    }

    public function ver(): void
    {
        $p = Producto::obtener(entero($_GET['id'] ?? 0)) ?? $this->noEncontrado('El producto');
        $this->render('productos/ver', ['p' => $p, 'ubicaciones' => Producto::ubicaciones()], $p['nombre']);
    }

    public function crear(): void
    {
        $this->formulario(null);
    }

    public function editar(): void
    {
        $p = Producto::obtener(entero($_GET['id'] ?? 0)) ?? $this->noEncontrado('El producto');
        $this->formulario($p);
    }

    private function formulario(?array $p): void
    {
        $this->render('productos/form', [
            'p' => $p, 'categorias' => Producto::categorias(), 'marcas' => Producto::marcas(),
        ], $p ? 'Editar producto' : 'Nuevo producto');
    }

    public function guardar(): void
    {
        $id = entero($_POST['id'] ?? 0) ?: null;
        $d = [
            'categoria_id' => entero($_POST['categoria_id'] ?? 0),
            'marca_id'     => entero($_POST['marca_id'] ?? 0),
            'nombre'       => texto($_POST['nombre'] ?? '', 120),
            'descripcion'  => texto($_POST['descripcion'] ?? '', 255),
            'precio_venta' => decimal($_POST['precio_venta'] ?? ''),
            'costo'        => decimal($_POST['costo'] ?? '0') ?? 0,
        ];
        try {
            if ($d['nombre'] === '' || !$d['categoria_id'] || !$d['marca_id']) {
                throw new NegocioException('Indique el nombre del modelo, la categoría y la marca.');
            }
            if ($d['precio_venta'] === null || $d['precio_venta'] < 0 || $d['costo'] < 0) {
                throw new NegocioException('Indique un precio de venta y un costo válidos.');
            }
            $id = Database::transaction(function () use ($d, $id) {
                $pid = Producto::guardar($d, $id);
                if (!empty($_POST['colores']) && !empty($_POST['tallas'])) {
                    Producto::crearVariantes($pid, texto($_POST['colores']), texto($_POST['tallas']), max(0, entero($_POST['stock_minimo'] ?? 1, 1)));
                }
                return $pid;
            });
            Producto::guardarFoto($id, $_FILES['foto'] ?? []);
            flash('success', 'Producto guardado.' . (empty($_POST['id']) ? ' Ahora puede registrar su ingreso de mercadería.' : ''));
            redirect('productos/ver', ['id' => $id]);
        } catch (NegocioException $e) {
            flash('danger', $e->getMessage());
            guardar_old($_POST);
            $id ? redirect('productos/editar', ['id' => $id]) : redirect('productos/crear');
        }
    }

    /** Agrega variantes (colores × tallas) a un producto existente. */
    public function variantes(): void
    {
        $pid = entero($_POST['producto_id'] ?? 0);
        try {
            $n = Producto::crearVariantes($pid, texto($_POST['colores'] ?? ''), texto($_POST['tallas'] ?? ''), max(0, entero($_POST['stock_minimo'] ?? 1, 1)));
            flash($n ? 'success' : 'info', $n ? "Se crearon $n variante(s)." : 'Esas combinaciones ya existían.');
        } catch (NegocioException $e) {
            flash('danger', $e->getMessage());
        }
        redirect('productos/ver', ['id' => $pid]);
    }

    /** Edita el stock mínimo o activa/desactiva una variante. */
    public function variante(): void
    {
        $v = Producto::variante(entero($_POST['id'] ?? 0)) ?? $this->noEncontrado('La variante');
        if (isset($_POST['stock_minimo'])) {
            Database::query('UPDATE variante SET stock_minimo = ? WHERE id = ?', [max(0, entero($_POST['stock_minimo'])), $v['id']]);
            flash('success', 'Stock mínimo actualizado.');
        }
        if (isset($_POST['alternar'])) {
            Database::query('UPDATE variante SET activo = 1 - activo WHERE id = ?', [$v['id']]);
            flash('success', $v['activo'] ? 'Variante desactivada: ya no aparecerá en las búsquedas.' : 'Variante activada.');
        }
        redirect('productos/ver', ['id' => $v['producto_id']]);
    }

    public function activar(): void
    {
        $id = entero($_POST['id'] ?? 0);
        Database::query('UPDATE producto SET activo = 1 - activo WHERE id = ?', [$id]);
        flash('success', 'Estado del producto actualizado.');
        redirect('productos/ver', ['id' => $id]);
    }

    /** Sube o reemplaza la foto de un color del modelo. */
    public function fotoColor(): void
    {
        $pid = entero($_POST['producto_id'] ?? 0);
        $color = texto($_POST['color'] ?? '', 40);
        try {
            Producto::guardarFotoColor($pid, $color, $_FILES['foto'] ?? []);
            flash('success', "Foto del color $color guardada.");
        } catch (NegocioException $e) {
            flash('danger', $e->getMessage());
        }
        redirect('productos/ver', ['id' => $pid]);
    }

    public function quitarFotoColor(): void
    {
        $pid = entero($_POST['producto_id'] ?? 0);
        $color = texto($_POST['color'] ?? '', 40);
        Producto::quitarFotoColor($pid, $color);
        flash('success', "Se quitó la foto del color $color; se mostrará la foto principal.");
        redirect('productos/ver', ['id' => $pid]);
    }

    public function kardex(): void
    {
        $v = Producto::variante(entero($_GET['id'] ?? 0)) ?? $this->noEncontrado('La variante');
        $this->render('productos/kardex', ['v' => $v, 'movs' => Producto::kardex((int) $v['id'])], 'Kardex ' . $v['codigo']);
    }
}
