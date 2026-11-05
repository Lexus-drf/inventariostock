<?php
/** Categorías y marcas (RF03). Solo administrador. */
class CatalogoController extends Controller
{
    private const TABLAS = ['categoria' => 'Categoría', 'marca' => 'Marca'];

    public function antesDe(string $accion): void
    {
        parent::antesDe($accion);
        $this->soloAdmin();
    }

    public function index(): void
    {
        $datos = [];
        foreach (array_keys(self::TABLAS) as $t) {
            $datos[$t] = Database::all("SELECT x.*, (SELECT COUNT(*) FROM producto p WHERE p.{$t}_id = x.id) AS productos
                                          FROM $t x ORDER BY x.activo DESC, x.nombre");
        }
        $this->render('catalogo/index', $datos, 'Categorías y marcas');
    }

    public function guardar(): void
    {
        $tabla = (string) ($_POST['tabla'] ?? '');
        $nombre = texto($_POST['nombre'] ?? '', 60);
        $id = entero($_POST['id'] ?? 0);
        if (!isset(self::TABLAS[$tabla]) || $nombre === '') {
            flash('danger', 'Escriba un nombre.');
            redirect('catalogo');
        }
        if (Database::value("SELECT 1 FROM $tabla WHERE nombre = ? AND id <> ?", [$nombre, $id])) {
            flash('danger', self::TABLAS[$tabla] . " \"$nombre\" ya existe.");
            redirect('catalogo');
        }
        $id ? Database::query("UPDATE $tabla SET nombre = ? WHERE id = ?", [$nombre, $id])
            : Database::insert("INSERT INTO $tabla (nombre) VALUES (?)", [$nombre]);
        flash('success', self::TABLAS[$tabla] . ' guardada.');
        redirect('catalogo');
    }

    public function alternar(): void
    {
        $tabla = (string) ($_POST['tabla'] ?? '');
        if (isset(self::TABLAS[$tabla])) {
            Database::query("UPDATE $tabla SET activo = 1 - activo WHERE id = ?", [entero($_POST['id'] ?? 0)]);
            flash('success', 'Estado actualizado.');
        }
        redirect('catalogo');
    }
}
