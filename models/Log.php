<?php

class Log {
    public $id;
    public $usuario_id;
    public $accion;
    public $descripcion;
    public $fecha;

    public function __construct($id, $usuario_id, $accion, $descripcion, $fecha) {
        $this->id = $id;
        $this->usuario_id = $usuario_id;
        $this->accion = $accion;
        $this->descripcion = $descripcion;
        $this->fecha = $fecha;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuario_id,
            'accion' => $this->accion,
            'descripcion' => $this->descripcion,
            'fecha' => $this->fecha
        ];
    }
}
?>