<?php
/** Consulta de disponibilidad (RF05): la pantalla más usada al atender. */
class BuscarController extends Controller
{
    public function index(): void
    {
        $filtros = [
            'q'         => texto($_GET['q'] ?? '', 80),
            'categoria' => entero($_GET['categoria'] ?? 0),
            'marca'     => entero($_GET['marca'] ?? 0),
            'talla'     => texto($_GET['talla'] ?? '', 10),
            'color'     => texto($_GET['color'] ?? '', 40),
            'con_stock' => !empty($_GET['con_stock']),
        ];
        $hayFiltro = $filtros['q'] !== '' || $filtros['categoria'] || $filtros['marca']
                  || $filtros['talla'] !== '' || $filtros['color'] !== '';

        $this->render('buscar/index', [
            'filtros'     => $filtros,
            'productos'   => $hayFiltro ? Producto::buscar($filtros) : [],
            'hayFiltro'   => $hayFiltro,
            'categorias'  => Producto::categorias(),
            'marcas'      => Producto::marcas(),
            'ubicaciones' => Producto::ubicaciones(),
        ], 'Buscar productos');
    }

    /** Sugerencias para los selectores de artículo (JSON). */
    public function api(): void
    {
        $this->json(Producto::opcionesVariantes(texto($_GET['q'] ?? '', 80)));
    }
}
