<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Support\Facades\Session;

class Usuario extends Model
{
    function leer_config($rutaFicheroConf)
    {
        // se cargan los datos del fichero XML
        $datos = simplexml_load_file($rutaFicheroConf);
        $usu = $datos->xpath("//usuario");
        $clave = $datos->xpath("//clave");
        $resul = [];

        $resul[] = (string) $usu[0];
        $resul[] = (string) $clave[0];
        return $resul;
    }

    // Método para comprobar si el nombre y la clave coinciden con los valores del XML
    function comprobar_usuario($nombre, $clave)
    {
        // Llamamos al método leer_config para obtener el usuario y la clave desde el XML
        $config = $this->leer_config(Storage::disk("xml")->path("configuracion.xml"));

        // Comparamos el nombre y la clave con los valores del XML
        return $nombre == $config[0] && $clave == $config[1];
    }
    function generarCodigo()
    {
        $codigoUsuario = 0;
        $obtenerAccesos = $this->getAccesos();
        $existe = true;
        while ($existe == true) {
            $existe = false;
            $codigoUsuario++;
            for ($i = 0; $i < count($obtenerAccesos); $i++) {
                if ($codigoUsuario == $obtenerAccesos[$i]['idsesion']) {
                    $existe = true;
                }
            }
        }
        return $codigoUsuario;
    }
    function getAccesos()
    {
        $rutaFichero = Storage::disk("datos")->path("info_accesos.dat");
        $obtenerAccesos = fopen($rutaFichero, "r");
        $listaAccesos = [];

        while ($usuario = fgets($obtenerAccesos)) {
            if (feof($obtenerAccesos)) {
                break;
            }
            $usuarioSeparado = explode("#", $usuario);
            if(empty(trim($usuarioSeparado[0]))){
                continue;
            }
            $datosUsuario = ["idsesion" => $usuarioSeparado[0], "usuario" => $usuarioSeparado[1], "inicio" => $usuarioSeparado[2], "fin" => trim($usuarioSeparado[3])];
            $listaAccesos[] = $datosUsuario;

        }
        return $listaAccesos;
    }
    function guardarAccesos()
    {
        $rutaFichero = Storage::disk("datos")->path("info_accesos.dat");
        if (!file_exists($rutaFichero)) {
            $ficheroDatos = fopen($rutaFichero, "w");
            fclose($ficheroDatos);
        }
        $codigoUsuario = $this->generarCodigo();
        $usuario = Session::get("Usuario");
        $fecha = date("Y:m:d H:i:s");
        $abrirFichero = fopen($rutaFichero, "a");
        fwrite($abrirFichero, (string) $codigoUsuario . "#" . $usuario . "#" . $fecha . "#\n");
        fclose($abrirFichero);
        return $codigoUsuario;
    }

    function guardarCierres()
    {
        $rutaFichero = Storage::disk("datos")->path("info_accesos.dat");
        if (!file_exists($rutaFichero)) {
            return false;
        }
        if (Session::has("IdSesion")) {
            $codigoUsuario = Session::get("IdSesion");
            $usuario = Session::get("Usuario");
            $fecha = date("Y:m:d H:i:s");
            $obtenerAccesos = $this->getAccesos();
            $abrirFichero = fopen($rutaFichero, "w");

            foreach ($obtenerAccesos as $posicion => $Acceso) {
                if ($Acceso["idsesion"] == $codigoUsuario) {
                    $obtenerAccesos[$posicion]["fin"] = $fecha;
                }

                fwrite($abrirFichero, (string) $Acceso["idsesion"] . "#" . $Acceso["usuario"] . "#" . $Acceso["inicio"] . "#$fecha\n");
            }

            fclose($abrirFichero);
        }

    }
}

