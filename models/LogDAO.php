<?php
require_once __DIR__ . '/../database/DBConnection.php';
require_once 'Log.php';

class LogDAO {
    private $conn;

    public function __construct() {
        $this->conn = DBConnection::getInstance()->getConnection();
    }

    public function registrarLog($usuario_id, $accion, $descripcion) {
        $sql = "INSERT INTO logs (usuario_id, accion, descripcion) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Error en prepare logs: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param('iss', $usuario_id, $accion, $descripcion);
        return $stmt->execute();
    }

    public function obtenerLogs($filtros = []) {
        $sql = "SELECT l.*, u.nombre AS usuario_nombre 
                FROM logs l 
                LEFT JOIN usuarios u ON l.usuario_id = u.id 
                WHERE 1=1";

        $params = [];
        $types = '';

        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND l.usuario_id = ?";
            $params[] = $filtros['usuario_id'];
            $types .= 'i';
        }

        if (!empty($filtros['desde'])) {
            $sql .= " AND l.fecha >= ?";
            $params[] = $filtros['desde'];
            $types .= 's';
        }

        if (!empty($filtros['hasta'])) {
            $sql .= " AND l.fecha <= ?";
            $params[] = $filtros['hasta'];
            $types .= 's';
        }

        $sql .= " ORDER BY l.fecha DESC";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Error en prepare logs: " . $this->conn->error);
            return [];
        }

        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }

        return $logs;
    }
}
