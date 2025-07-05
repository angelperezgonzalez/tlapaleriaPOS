<?php
session_start();
require_once __DIR__ . '/../models/UsuarioDAO.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    $dao = new UsuarioDAO();
    $usuario = $dao->obtenerPorEmail($email);

    if (!$usuario) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
        exit;
    }

    // Aquí puedes cambiar a password_verify si usas hash
    //if ($usuario->password === $password) {
    if(password_verify($password,$usuario->password)){
        $_SESSION['usuario'] = [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'rol' => $usuario->rol
        ];
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Contraseña incorrecta']);
        
    }
    exit;
}

if ($method === 'GET') {
    echo json_encode($_SESSION['usuario'] ?? null);
    exit;
}
