<?php
require_once __DIR__ . '/../database/DBConnection.php';
require_once 'Venta.php';
require_once 'DetalleVenta.php';

class VentaDAO {
    private $conn;

    public function __construct() {
        $this->conn = DBConnection::getInstance()->getConnection();
    }

    public function registrarVenta($detalles) {
        $this->conn->begin_transaction();

        try {
            $total = 0;
            foreach($detalles as $d){
                $total += $d['cantidad'] * $d['precio_unitario'];
            }

            // Insertar venta
            $sql = "INSERT INTO ventas (total) VALUES (?)";
            $stmt_venta = $this->conn->prepare($sql);

            if (!$stmt_venta) die("Error en prepare ventas: " . $this->conn->error);
            $stmt_venta->bind_param('d', $total);
            if (!$stmt_venta->execute()) die("Error ejecutando ventas: " . $stmt_venta->error);

            $ventaId = $this->conn->insert_id;

            //$this->conn->query("INSERT INTO ventas () VALUES ()");
            //$ventaId = $this->conn->insert_id;

            // Insertar detalles y actualizar stock
            foreach ($detalles as $d) {
                $stmt = $this->conn->prepare("INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $ventaId, $d['producto_id'], $d['cantidad'], $d['precio_unitario']);
                $stmt->execute();

                // Descontar stock
                $stmt2 = $this->conn->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
                $stmt2->bind_param("ii", $d['cantidad'], $d['producto_id']);
                $stmt2->execute();
            }

            $this->conn->commit();
            return $ventaId;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function obtenerVentas() {
        $result = $this->conn->query("SELECT * FROM ventas ORDER BY fecha DESC");
        $ventas = [];

        while ($row = $result->fetch_assoc()) {
            $ventas[] = new Venta($row['id'], $row['fecha'], $row['total']);
        }

        return $ventas;
    }

    public function obtenerDetallesPorVenta($ventaId) {
        $stmt = $this->conn->prepare("SELECT * FROM detalle_ventas WHERE venta_id = ?");
        $stmt->bind_param("i", $ventaId);
        $stmt->execute();
        $result = $stmt->get_result();

        $detalles = [];
        while ($row = $result->fetch_assoc()) {
            $detalles[] = new DetalleVenta($row['id'], $row['venta_id'], $row['producto_id'], $row['cantidad'], $row['precio_unitario']);
        }

        return $detalles;
    }

    public function getDetalleVenta($ventaId) {
        $detalles = [];
        $sql = "SELECT dv.producto_id, p.nombre, dv.cantidad, dv.precio_unitario
                FROM detalle_ventas dv
                JOIN productos p ON dv.producto_id = p.id
                WHERE dv.venta_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $ventaId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $detalles[] = $row;
        }
        return $detalles;
    }

    public function getVentaById($ventaId) {
        $sql = "SELECT id, fecha, total FROM ventas WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $ventaId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function obtenerTodas() {
        $sql = "SELECT id, fecha, total FROM ventas ORDER BY fecha DESC";
        $result = $this->conn->query($sql);
    
        $ventas = [];
        while ($row = $result->fetch_assoc()) {
            $ventas[] = $row;
        }
        return $ventas;
    }

    public function obtenerVentasPorRango($inicio, $fin) {
        $sql = "SELECT id, fecha, total FROM ventas WHERE DATE(fecha) BETWEEN ? AND ? ORDER BY fecha DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) die("Error en prepare: " . $this->conn->error);
    
        $stmt->bind_param('ss', $inicio, $fin);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $ventas = [];
        while ($row = $result->fetch_assoc()) {
            $ventas[] = new Venta($row['id'], $row['fecha'], $row['total']);
        }
        return $ventas;
    }    


}
?>