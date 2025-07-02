<?php
class DetalleVenta {
    public $id;
    public $venta_id;
    public $producto_id;
    public $cantidad;
    public $precio_unitario;

    public function __construct($id, $venta_id, $producto_id, $cantidad, $precio_unitario) {
        $this->id = $id;
        $this->venta_id = $venta_id;
        $this->producto_id = $producto_id;
        $this->cantidad = $cantidad;
        $this->precio_unitario = $precio_unitario;
    }
}
?>