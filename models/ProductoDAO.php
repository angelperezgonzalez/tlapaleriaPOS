<?php
require_once __DIR__ . '/../database/DBConnection.php';
require_once 'Producto.php';

class ProductoDAO {
    private $conn;

    public function __construct() {
        $this->conn = DBConnection::getInstance()->getConnection();
    }

    public function obtenerTodos() {
        $result = $this->conn->query("SELECT id, nombre, precio, stock FROM productos");
        $productos = [];

        while ($row = $result->fetch_assoc()) {
            $productos[] = new Producto($row['id'], $row['nombre'], $row['precio'], $row['stock']);
        }

        return $productos;
    }

    public function agregar($nombre, $precio, $stock) {
        $stmt = $this->conn->prepare("INSERT INTO productos (nombre, precio, stock) VALUES (?, ?, ?)");
        $stmt->bind_param("sdi", $nombre, $precio, $stock);
        return $stmt->execute();
    }

    public function actualizarStock($id, $nuevoStock) {
        $stmt = $this->conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $nuevoStock, $id);
        return $stmt->execute();
    }

    public function obtenerPorId($id) {
        $stmt = $this->conn->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return new Producto($row['id'], $row['nombre'], $row['precio'], $row['stock']);
        }

        return null;
    }
}
?>