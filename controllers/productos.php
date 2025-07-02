<?php
header("Content-Type: application/json");
require_once '../models/ProductoDAO.php';

// Instancia del DAO
$dao = new ProductoDAO();

// Manejo del método HTTP
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Obtener todos los productos
    $productos = $dao->obtenerTodos();

    // Convertir a array para JSON
    $result = [];
    foreach ($productos as $p) {
        $result[] = [
            'id' => $p->id,
            'nombre' => $p->nombre,
            'precio' => $p->precio,
            'stock' => $p->stock
        ];
    }

    echo json_encode($result);
    exit;
}

if ($method === 'POST') {
    // Leer datos enviados
    $nombre = $_POST['nombre'] ?? null;
    $precio = $_POST['precio'] ?? null;
    $stock = $_POST['stock'] ?? null;

    if (!$nombre || !$precio || !$stock) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        exit;
    }

    $exito = $dao->agregar($nombre, $precio, $stock);

    if ($exito) {
        echo json_encode(['status' => 'ok']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'No se pudo guardar']);
    }
    exit;
}

// Método no permitido
http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);