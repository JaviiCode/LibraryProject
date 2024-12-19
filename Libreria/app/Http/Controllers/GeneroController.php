<?php

namespace App\Http\Controllers;
use App\Models\Genero;
use Illuminate\Http\Request;

class GeneroController extends Controller
{
    public function obtenerGeneros(){
        $genero = new Genero();
        return json_encode($genero->getGeneros());
    }
}

