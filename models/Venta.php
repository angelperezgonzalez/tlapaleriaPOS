<?php
class Venta {
    public $id;
    public $fecha;
    public $total;

    public function __construct($id, $fecha, $total) {
        $this->id = $id;
        $this->fecha = $fecha;
        $this->total = $total;
    }
}
?>