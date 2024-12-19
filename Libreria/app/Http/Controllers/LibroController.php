<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Http\Request;

class LibroController extends Controller
{
    public function obtenerLibros(){
        $libros = new Libro();
        return json_encode($libros->getLibros());
    }
}

