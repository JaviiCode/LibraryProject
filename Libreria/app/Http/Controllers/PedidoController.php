<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PedidoController extends Controller
{
    public function procesarPedido() {
        $pedido = new Pedido();

        $procesado = $pedido->procesarPedido();

        if ($procesado) {
            Session::put("Carrito", []);
            return view("Procesar_Pedido", ["mensaje"=>"Pedido realizado con éxito. Se enviará un correo de cofirmación"]);

        } else {

            return view("Procesar_Pedido", ["mensaje"=>"El pedido no se puede realizar. El carrito está vacío..."]);
        }
    }

    public function obtenerPedidos() {
        $pedido = new Pedido();
        $libro = new Libro();

        $pedidos = $pedido->cargarPedidos();

        $pedidosInfo = [];

        foreach ($pedidos as $pedidoItem) {
            $libroInfo = $libro->buscarLibro($pedidoItem["isbn"]);
            $libroInfo["unidades"] = $pedidoItem["unidades"];
            $libroInfo["usuario"] = $pedidoItem["usuario"];
            $libroInfo["fechapedido"] = $pedidoItem["fechapedido"];
            $libroInfo["codpedido"] = $pedidoItem["codpedido"];

            $pedidosInfo[] = $libroInfo;
        }

        return json_encode($pedidosInfo);
    }

    public function cancelarPedido(Request $datos) {
        if (!$datos->has("codpedido")) {
            return json_encode([
                "respuesta" => false,
                "error"=>"Faltan parámetros"
            ]);
        }

        $pedido = new Pedido();

        $pedido->cancelarPedido($datos->codpedido);

        return json_encode([
            "respuesta"=>true,
            "error"=>""
        ]);

    }
}
