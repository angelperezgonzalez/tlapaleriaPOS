<?php

session_start();
require_once __DIR__ . '/../models/LogDAO.php';
require_once __DIR__ . '/../models/Log.php';

// Verifica método
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $dao = new LogDAO();

    // Leer filtros de la URL
    $filtros = [];

    if (!empty($_GET['usuario_id'])) {
        $filtros['usuario_id'] = intval($_GET['usuario_id']);
    }

    if (!empty($_GET['desde'])) {
        $filtros['desde'] = $_GET['desde'];
    }

    if (!empty($_GET['hasta'])) {
        $filtros['hasta'] = $_GET['hasta'];
    }

    // Obtener logs filtrados
    $logs = $dao->obtenerLogs($filtros);

    // Devolver en JSON
    $result = array_map(function($log) {
        if ($log instanceof Log) {
            return $log->toArray();
        }
        return $log;  // por si tu DAO devuelve arrays en lugar de objetos
    }, $logs);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
