<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Genero extends Model
{
    public function getGeneros(){
        $contenidoFichero = Storage::disk("xml")->get("libros.xml");
        $datos = simplexml_load_string($contenidoFichero);
        $generosxml = $datos->xpath("//genero");
        $generos = [];
        foreach($generosxml as $cod => $nombre){
            $generos[] = ["cod"=>$cod, "nombre"=>(string)$nombre];
        }return $generos;
    }
}
