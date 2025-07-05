<?php

class Usuario {
    public $id;
    public $nombre;
    public $email;
    public $rol;
    public $password;

    public function __construct($id, $nombre, $email, $rol, $password = '') {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->email = $email;
        $this->rol = $rol;
        $this->password = $password;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'rol' => $this->rol
        ];
    }
}
?>