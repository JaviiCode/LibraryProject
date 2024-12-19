<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class Carrito extends Model
{
    public function sumarLibros($isbn, $cantidad) {
        $carrito = $this->getCarrito();
        $enCarrito = false;
        foreach ($carrito as $posicion => $libro) {
            if ($libro["isbn"] == $isbn) {
                $carrito[$posicion]["unidades"] += $cantidad;
                $enCarrito = true;
            }
        }

        if (!$enCarrito) {
            $carrito[] = [
                "isbn"=>$isbn,
                "unidades"=>$cantidad
            ];
        }

        Session::put("Carrito", $carrito);
    }
    public function eliminarLibros($isbn, $cantidad) {
        $carrito = $this->getCarrito();
        foreach ($carrito as $posicion => $libro) {
            if ($libro["isbn"] == $isbn) {
                if ($libro["unidades"]-$cantidad <= 0) {
                    unset($carrito[$posicion]);

                } else {
                    $carrito[$posicion]["unidades"] -= $cantidad;
                }
            }
        }

        Session::put("Carrito", $carrito);
    }

    public function getCarrito() {
        if (Session::has("Carrito")) {
            $carrito = Session::get("Carrito");
            return $carrito;
        } else {
            return [];
        }
    }
}
