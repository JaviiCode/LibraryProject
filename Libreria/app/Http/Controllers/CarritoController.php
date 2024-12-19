<?php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\Libro;
use Illuminate\Http\Request;

class CarritoController extends Controller
{
    public function añadirLibros(Request $request) {
        $validarRequest = $request->validate(["isbn"=>"required", "cantidad"=>"required"]);

        $carrito = new Carrito();

        if (!$validarRequest) {
            return json_encode([
                "respuesta"=>false,
                "error"=>"Faltan parámetros"
            ]);
        }

        $carrito->sumarLibros($request->isbn, $request->cantidad);

        return json_encode(["respuesta" => true, "err"]);

    }
    public function eliminarLibros(Request $request) {
        $validarRequest = $request->validate(["isbn"=>"required", "cantidad"=>"required"]);

        $carrito = new Carrito();

        if (!$validarRequest) {
            return json_encode([
                "respuesta"=>false,
                "error"=>"Faltan parámetros"
            ]);
        }

        $carrito->eliminarLibros($request->isbn, $request->cantidad);

        return json_encode(["respuesta" => true, "err"]);

    }

    public function cargarCarrito() {
        $carrito = new Carrito();
        $libro = new Libro();

        $listaCarrito = $carrito->getCarrito();

        $listaLibrosCarrito = [];

        $nArticulos = 0;
        $nUnidades = 0;

        $listaLibrosCarrito[0] = [
            "numunidades"=>$nUnidades,
            "numarticulos"=>$nArticulos
        ];

        foreach ($listaCarrito as $libroCarrito) {
            $libroInfo = $libro->buscarLibro($libroCarrito["isbn"]);

            $listaLibrosCarrito[] = $libroInfo;

            $nUnidades += $libroInfo["unidades"];
            $nArticulos++;
        }

        $listaLibrosCarrito[0] = [
            "numunidades"=>$nUnidades,
            "numarticulos"=>$nArticulos
        ];

        return json_encode((array)$listaLibrosCarrito);
    }
}
