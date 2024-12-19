<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Libro extends Model
{
    public function getLibros(){
        $contenidoFichero = Storage::disk("xml")->get("libros.xml");
        $datos = simplexml_load_string($contenidoFichero);
        $librosxml = $datos->xpath("//libro");
        $libros = [];
        $carrito = new Carrito();
        $listaCarrito = $carrito->getCarrito();
        foreach($librosxml as $libro){
            $nuevoLibro = [];
            foreach($libro as $atributo => $valor){
                $nuevoLibro[$atributo] = (string)$valor;
            }
            //agrego la cantidad que tiene en el carrito
            $nuevoLibro["unidades"] = 0;
            foreach ($listaCarrito as $libroCarrito) {
                if ($libroCarrito["isbn"] == $nuevoLibro["isbn"]) {
                    $nuevoLibro["unidades"] = $libroCarrito["unidades"];
                }
            }
            $libros[] = $nuevoLibro;
        }
        return $libros;
    }

    public function buscarLibro($isbn) {
        $libros = $this->getLibros();

        foreach ($libros as $libro) {
            if ($libro["isbn"] == $isbn) {
                return $libro;
            }
        }

        return false;
    }
}
