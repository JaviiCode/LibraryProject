<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use \Illuminate\Support\Facades\Session;


class UsuarioController extends Controller
{
    public function login()
    {
        $sesiones = new Usuario();
        $usuario = $sesiones->comprobar_usuario($_POST['usuario'], $_POST['clave']);
        if ($usuario == FALSE) {
            return json_encode([
                "respuesta"=>false,
                "error"=>""
            ]);
        } else {
            session_start();
            Session::put("Usuario", $_POST['usuario']);
            $codigoSesion = $sesiones -> guardarAccesos();
            Session::put("IdSesion", $codigoSesion);
            return json_encode([
                "respuesta"=>true,
                "error"=>""
            ]);
        }

    }

    public function usuarioExiste(){
        if(Session::has("Usuario")){
            return response(json_encode([
                "respuesta"=> session::get("Usuario"),
                "error"=> ""
            ]));
        }else{
            return json_encode([
                "respuesta"=>false,
                "error"=> ""
            ]);
        }
    }
    public function logout(){
        $sesiones = new Usuario();
        $sesiones -> guardarCierres();
        session::flush();
        session::regenerate();
        session::start();
        return json_encode(csrf_token());

    }
    public function cargarAccesos(){
        $accesos = new Usuario();
        $obtenerAccesos = $accesos -> getAccesos();
        return json_encode($obtenerAccesos);
    }
}
