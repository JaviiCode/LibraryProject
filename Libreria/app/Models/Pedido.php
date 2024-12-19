<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class Pedido extends Model
{

    public function cargarPedidos() {
        $pedidos = [];
        $rutaPedidos = Storage::disk("datos")->path("pedidos.dat");

        if (!Storage::disk("datos")->exists("pedidos.dat")) {
            return [];
        }

        $ficheroPedidos = fopen($rutaPedidos, "r");

        while ($pedidoLinea = fgets($ficheroPedidos)) {
            $pedidoDividido = explode("#", $pedidoLinea);
            $nuevoPedido = [
                "codpedido"=>trim($pedidoDividido[0]),
                "usuario"=>trim($pedidoDividido[1]),
                "fechapedido"=>trim($pedidoDividido[2]),
                "isbn"=>trim($pedidoDividido[3]),
                "unidades"=>trim($pedidoDividido[4])
            ];
            $nuevoPedido["unidades"] = str_replace("@", "", $nuevoPedido["unidades"]);

            $pedidos[] = $nuevoPedido;

            if (feof($ficheroPedidos)) {
                break;
            }
        }

        return $pedidos;
    }

    public function generarPedidoCodigo() {
        $nuevoCodigo = 0;
        $existeCodigo = true;
        $pedidos = $this->cargarPedidos();

        while ($existeCodigo) {
            $existeCodigo = false;
            $nuevoCodigo++;

            foreach ($pedidos as $pedido) {
                if ($pedido["codpedido"] ==  $nuevoCodigo) {
                    $existeCodigo = true;
                }
            }
        }

        return $nuevoCodigo;
    }

    public function procesarPedido() {
        $rutaPedidoFichero = Storage::disk("datos")->path("pedidos.dat");

        if (!Storage::disk("datos")->exists("pedidos.dat")) {
            $abrirFichero = fopen($rutaPedidoFichero, "w");
            fclose($abrirFichero);
        }

        if  (!Session::has("Carrito") || !count(Session::get("Carrito"))) {
            return false;
        }

        $abrirFichero = fopen($rutaPedidoFichero, "a");

        $listaCarrito = Session::get("Carrito");
        $usuario = Session::get("Usuario");
        $fecha = date("Y:m:d H:i:s");

        //recorro la lista de items dentro de carrito
        foreach ($listaCarrito as $libro) {
            $isbn = $libro["isbn"];
            $unidades = $libro["unidades"];
            $codigopedido = $this->generarPedidoCodigo();

            fwrite($abrirFichero, (string)$codigopedido."#".$usuario."#".$fecha."#".$isbn."#".$unidades."@\n");
        }

        fclose($abrirFichero);

        return true;


    }

    public function cancelarPedido($codpedido) {
        $pedidos = $this->cargarPedidos();
        $rutaPedidos = Storage::disk("datos")->path("pedidos.dat");

        foreach ($pedidos as $posicion => $pedido) {
            if ($pedido["codpedido"]==$codpedido) {
                unset($pedidos[$posicion]);
            }
        }

        //escribir la modificacion del array al fichero pedidos.dat
        $abrirFichero = fopen($rutaPedidos, "w");
        $usuario = Session::get("Usuario");
        $fecha = date("Y:m:d H:i:s");

        foreach($pedidos as $pedido) {
            $isbn = $pedido["isbn"];
            $unidades = $pedido["unidades"];
            $codigopedido = $pedido["codpedido"];

            fwrite($abrirFichero, (string)$codigopedido."#".$usuario."#".$fecha."#".$isbn."#".$unidades."@\n");
        }

        fclose($abrirFichero);

        return true;

    }
}
