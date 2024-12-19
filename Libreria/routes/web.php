<?php

use App\Http\Controllers\CarritoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('Principal');
})->name("login");

Route::post('login_json', [UsuarioController::class, 'login'])->name("login");
Route::get('islogged', [UsuarioController::class, 'usuarioExiste'])->name("login_validar");
Route::get('LogOut', [UsuarioController::class, 'logout'])->name("login_cerrar");
Route::get('Libros', [LibroController::class, 'obtenerLibros'])->name("mostrar_libros");
Route::get('Generos', [GeneroController::class, 'obtenerGeneros'])->name("mostrar_generos");
Route::get('Accesos', [UsuarioController::class, 'cargarAccesos'])->name("obtener_accesos");
Route::get("Carrito", [CarritoController::class, "cargarCarrito"])->name("mostrar_carrito");
Route::post("AñadirLibros", [CarritoController::class, "añadirLibros"])->name("añadir_libros");
Route::post("EliminarLibros", [CarritoController::class, "eliminarLibros"])->name("eliminar_libros");
Route::get("ProcesarPedido", [PedidoController::class, "procesarPedido"])->name("procesar_pedido");
Route::get("Pedidos", [PedidoController::class, "obtenerPedidos"])->name("mostrar_pedidos");
Route::post("CancelarPedido", [PedidoController::class, "cancelarPedido"])->name("cancelar_pedidos");
