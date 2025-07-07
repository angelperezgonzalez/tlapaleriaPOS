<?php
session_start();
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

    if (isset($_POST['id']) && isset($_POST['stock'])) {
        // actualizar stock
        $id = intval($_POST['id']);
        $stock = intval($_POST['stock']);

        $viejoStock = $dao->obtenerPorId($id);

        if ($dao->actualizarStock($id, $stock)) {
            // Registrar en log
            require_once __DIR__ . '/../models/LogDAO.php';
            $logDao = new LogDAO();
            $usuario_id = $_SESSION['usuario']['id'] ?? null;
            $logDao->registrarLog($usuario_id, 'ACTUALIZAR STOCK', "Producto actualizado [$viejoStock->nombre][$id] precio [$viejoStock->precio ] stock[$viejoStock->stock] nuevo stock [$stock]");
            echo json_encode(['status' => 'ok']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar']);
        }
        exit;
    }

    if (!$nombre || !$precio || !$stock) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        exit;
    }

    $exito = $dao->agregar($nombre, $precio, $stock);

    if ($exito) {
        // Registrar en log
        require_once __DIR__ . '/../models/LogDAO.php';
        $logDao = new LogDAO();
        $usuario_id = $_SESSION['usuario']['id'] ?? null;
        $logDao->registrarLog($usuario_id, 'AGREGAR STOCK', "Producto nuevo [$nombre] precio[$precio] stock[$stock]");
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