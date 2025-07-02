<?php
require_once __DIR__ . '/../models/VentaDAO.php';

class RegistrarVentaCommand {
    private $ventaDAO;

    public function __construct() {
        $this->ventaDAO = new VentaDAO();
    }

    public function ejecutar($detalles) {
        return $this->ventaDAO->registrarVenta($detalles);
    }
}
?>