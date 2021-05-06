<?php


namespace App;

class Producto{
 
 public function __construct($nombre, $precio, $preciouni, $cantidad){
     $this->nombre = $nombre;
     $this->precio = $precio;
     $this->preciouni = $preciouni;
     $this->cantidad = $cantidad;
 }
}