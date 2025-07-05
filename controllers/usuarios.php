<?php
session_start();
require_once __DIR__ . '/../models/UsuarioDAO.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$dao = new UsuarioDAO();

switch ($method) {
    case 'POST':
        // Registrar usuario (solo ADMIN)
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'ADMIN') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Prohibido']);
            exit;
        }

        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $rol = $_POST['rol'] ?? 'CAJERO';

        if (!$nombre || !$email || !$password) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
            exit;
        }

        $result = $dao->crearUsuario($nombre, $email, $password, $rol);
        echo json_encode($result);
        exit;

    case 'GET':
        // Listar usuarios (solo ADMIN)
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'ADMIN') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Prohibido']);
            exit;
        }

        $result = array_map(function($u) { return $u->toArray(); }, $dao->obtenerUsuarios());
        echo json_encode($result);
        exit;

    case 'PUT':
        // Editar usuario
        parse_str(file_get_contents("php://input"), $putData);

        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'ADMIN') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Prohibido']);
            exit;
        }

        $id = $putData['id'] ?? null;
        $nombre = $putData['nombre'] ?? '';
        $email = $putData['email'] ?? '';
        $rol = $putData['rol'] ?? '';

        if (!$id || !$nombre || !$email || !$rol) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
            exit;
        }

        $result = $dao->actualizarUsuario($id, $nombre, $email, $rol);
        echo json_encode($result);
        exit;

    case 'DELETE':
        // Eliminar usuario
        parse_str(file_get_contents("php://input"), $deleteData);

        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'ADMIN') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Prohibido']);
            exit;
        }

        $id = $deleteData['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID faltante']);
            exit;
        }

        $result = $dao->eliminarUsuario($id);
        echo json_encode($result);
        exit;
}

http_response_code(405);
echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
