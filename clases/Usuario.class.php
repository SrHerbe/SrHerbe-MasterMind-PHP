<?php
/*
Título: Tarea 1 - Unidad 4

Autor: Samuel Hernández Berrocal

Fecha de modificación: 21/11/2022

Versión 1.0
*/
class Usuario
{
    private $nombre;
    private $contrasena;
    private $confirmcontrasena;
    private $rol;
    private $email;


    public function getNombre() { return $this->nombre;}
    public function getContrasena(){ return $this->contrasena;}
    public function getConfirmcontrasena(){ return $this->confirmcontrasena;}
    public function getRol() { return $this->rol;}
    public function getEmail() { return $this->email; }


    public function __construct($nombre, $contrasena, $confirmcontrasena, $rol, $email)
    {
        $this->nombre = $nombre;
        $this->contrasena = $contrasena;
        $this->confirmcontrasena = $confirmcontrasena;
        $this->rol = $rol;
        $this->email = $email;
    }

    //Devuelve un String con las keys de los atributos del usuario
    public function keys() {
        $keys = "";

        foreach ($_POST as $key => $value) {
            switch ($key) {
                case "token-csrf":
                    break;
                    //Si es "email" es el último valor y finalizaremos la línea
                case "email":
                    $keys = $keys."rol;".$key.";Eliminar\n";
                    break 2;
                default:
                    $keys = $keys.$key.";";
                    break;
            }
        }
        return $keys;
    }

    //Devuelve un String con los atributos de Usuario
    public function datos() {
        return $this->nombre.";".$this->contrasena.";".$this->confirmcontrasena.";".$this->rol.";".$this->email."\n";
    }

    //Devuelve true o false dependiendo si el usuario es administrador
    public function esAdmin() {
        if ($this->rol === "admin") {
            return true;
        } else {
            return false;
        }
    }
}
?>