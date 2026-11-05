<?php
/** Pantalla de inicio: búsqueda rápida, resumen del día y alertas. */
class InicioController extends Controller
{
    public function index(): void
    {
        $hoy = date('Y-m-d');
        $this->render('inicio/index', [
            'resumen'   => Reporte::resumenDia($hoy),
            'alertas'   => Auth::esAdmin() ? array_slice(Producto::alertas(), 0, 6) : [],
            'nAlertas'  => Auth::esAdmin() ? Producto::contarAlertas() : 0,
            'reservas'  => array_slice(Reserva::listar('ACTIVA'), 0, 5),
            'nReservas' => Reserva::contarActivas(),
            'semana'    => Auth::esAdmin() ? Reporte::ventasPorDia(date('Y-m-d', strtotime('-6 days')), $hoy) : [],
        ], 'Inicio');
    }
}
