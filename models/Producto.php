<?php
class Producto {
    public $id;
    public $nombre;
    public $precio;
    public $stock;

    public function __construct($id, $nombre, $precio, $stock) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->precio = $precio;
        $this->stock = $stock;
    }
}
?>