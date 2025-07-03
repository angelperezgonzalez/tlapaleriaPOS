<?php
require_once '../database/DBConnection.php';
require_once '../models/VentaDAO.php';

$ventaDAO = new VentaDAO();

$action = $_GET['action'] ?? '';

if ($action === 'listar') {
    $ventas = $ventaDAO->obtenerVentas();
    header('Content-Type: application/json');
    echo json_encode($ventas);
}

if ($action === 'detalle' && isset($_GET['id'])) {
    $detalles = $ventaDAO->getDetalleVenta(intval($_GET['id']));
    header('Content-Type: application/json');
    echo json_encode($detalles);
}

if ($action === 'ticket' && isset($_GET['id'])) {
    $ventaId = intval($_GET['id']);

    $venta = $ventaDAO->getVentaById($ventaId);
    $detalles = $ventaDAO->getDetalleVenta($ventaId);

    header('Content-Type: application/json');
    echo json_encode([
        'venta' => $venta,
        'detalles' => $detalles
    ]);
}
