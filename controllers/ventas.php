<?php
header("Content-Type: application/json");

require_once '../commands/RegistrarVentaCommand.php';
require_once '../models/VentaDAO.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Registro de nueva venta
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['detalles'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        exit;
    }

    $command = new RegistrarVentaCommand();
    $ventaId = $command->ejecutar($input['detalles']);

    if ($ventaId) {
        echo json_encode(['status' => 'ok', 'venta_id' => $ventaId]);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar venta']);
    }

    exit;
}

if ($method === 'GET') {
    $dao = new VentaDAO();

    if (isset($_GET['id'])) {
        // Obtener detalles de una venta específica
        $detalles = $dao->obtenerDetallesPorVenta($_GET['id']);
        $result = [];

        foreach ($detalles as $d) {
            $result[] = [
                'producto_id' => $d->producto_id,
                'cantidad' => $d->cantidad,
                'precio_unitario' => $d->precio_unitario
            ];
        }

        echo json_encode($result);
        exit;
    }elseif (isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin'])) {
        $inicio = $_GET['fecha_inicio'];
        $fin = $_GET['fecha_fin'];

        $ventas = $dao->obtenerVentasPorRango($inicio, $fin);

        $result = [];
        foreach ($ventas as $v) {
            $result[] = [
                'id' => $v->id,
                'fecha' => $v->fecha,
                'total' => $v->total
            ];
        }

        echo json_encode($result);
        exit;
    } else {

    // Obtener todas las ventas
    $ventas = $dao->obtenerVentas();
    $result = [];

    foreach ($ventas as $v) {
        $result[] = [
            'id' => $v->id,
            'fecha' => $v->fecha,
            'total' => $v->total
        ];
    }

    echo json_encode($result);
    exit;
    }
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
?>