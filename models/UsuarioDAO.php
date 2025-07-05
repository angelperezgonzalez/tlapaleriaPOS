<?php
require_once __DIR__ . '/../database/DBConnection.php';
require_once 'Usuario.php';

class UsuarioDAO {
    private $conn;

    public function __construct() {
        $this->conn = DBConnection::getInstance()->getConnection();
    }

    public function crearUsuario($nombre, $email, $password, $rol) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) return ['status' => 'error', 'message' => $this->conn->error];

        $stmt->bind_param("ssss", $nombre, $email, $hash, $rol);

        if ($stmt->execute()) {
            return ['status' => 'ok', 'message' => 'Usuario creado'];
        } else {
            return ['status' => 'error', 'message' => $stmt->error];
        }
    }

    public function obtenerUsuarios() {
        $result = $this->conn->query("SELECT id, nombre, email, rol FROM usuarios");
        $usuarios = [];

        while ($row = $result->fetch_assoc()) {
            $usuarios[] = new Usuario($row['id'], $row['nombre'], $row['email'], $row['rol']);
        }

        return $usuarios;
    }

    public function actualizarUsuario($id, $nombre, $email, $rol) {
        $sql = "UPDATE usuarios SET nombre=?, email=?, rol=? WHERE id=?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) return ['status' => 'error', 'message' => $this->conn->error];

        $stmt->bind_param("sssi", $nombre, $email, $rol, $id);

        if ($stmt->execute()) {
            return ['status' => 'ok', 'message' => 'Usuario actualizado'];
        } else {
            return ['status' => 'error', 'message' => $stmt->error];
        }
    }

    public function eliminarUsuario($id) {
        $sql = "DELETE FROM usuarios WHERE id=?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) return ['status' => 'error', 'message' => $this->conn->error];

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            return ['status' => 'ok', 'message' => 'Usuario eliminado'];
        } else {
            return ['status' => 'error', 'message' => $stmt->error];
        }
    }

    public function obtenerPorEmail($email) {
        $stmt = $this->conn->prepare("SELECT id, nombre, email, rol, password FROM usuarios WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
    
        if ($row = $res->fetch_assoc()) {
            return new Usuario(
                $row['id'],
                $row['nombre'],
                $row['email'],
                $row['rol'],
                $row['password']
            );
        }
    
        return null;
    }
    
}
