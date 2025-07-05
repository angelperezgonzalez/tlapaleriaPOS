<?php
require_once __DIR__ . '/../models/VentaDAO.php';

class ListarVentasCommand {
    public function ejecutar() {
        $dao = new VentaDAO();
        $ventas = $dao->obtenerVentas();
        return $ventas;
    }
}
