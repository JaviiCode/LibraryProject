let usuario = "";
let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');{}

async function ocultarVistas(){
        document.getElementById("principal").style.display = "none";
        document.getElementById("login").style.display = "none";
        document.getElementById("cab_usuario").innerHTML = "Usuario: " + usuario;}

async function login() {
    try {
        ocultarVistas();
        usuario = document.getElementById("usuario").value;
        const clave = document.getElementById("clave").value;
        const params = new URLSearchParams();
        params.append("usuario", usuario);
        params.append("clave", clave);

        const response = await fetch("login_json", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": token,
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: params.toString({ usuario, clave })

        });

        const responseJson = await response.json();

        if (response.ok) {
            if (responseJson.respuesta == false) {
                alert("Revise usuario y contraseña");
                window.location.href = "/";
                usuario = "";
                document.getElementById("login").style.display = "block";

            } else {
                document.getElementById("principal").style.display = "block";
                document.getElementById("cab_usuario").innerHTML = "Usuario: " + usuario;
            }
        } else {
            console.error("Error en la respuesta: ", response.status);
        }
    } catch (error) {
        console.error("Error en la petición:", error);
    }

    return false;
}
async function ComprobarInicioSesion() {
    const response = await fetch("islogged", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
    });
    const responsejson = await response.json();
    if (responsejson.respuesta.length) {
        ocultarVistas();
        document.getElementById("principal").style.display = "block";
        document.getElementById("cab_usuario").innerHTML = "Usuario: " + responsejson.respuesta;
        usuario = responsejson.respuesta;
    }
}

async function logout() {
    const response = await fetch("LogOut", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
    });
    const responsejson = await response.json();
    token = responsejson;
    ocultarVistas();
    document.getElementById("login").style.display = "block";
}
async function cargarLibros() {
    //Recibir datos a traves del web.php recogiendolo en el controller
    const response = await fetch("Libros", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
    });
    //Pasar de leible a formato json.
    const responsejson = await response.json();
    //Colocar columnas de las tablas
    ocultarVistas();
    vaciarLista();
    document.getElementById("principal").style.display = "block";
    document.getElementById("cab_usuario").innerHTML = "Usuario: " + usuario;
    vaciarTabla();

    let librosArray = [];

    for (let libro of responsejson) {
        const libroArray = [
            libro.isbn, libro.titulo, libro.escritores, libro.genero, libro.numpaginas, libro.imagen, libro.unidades
        ];

        librosArray.push(libroArray)
    }

    mostrarTablas(["isbn", "titulo", "escritores", "genero", "numpaginas", "imagen", "unidades"], librosArray, 5, ["input", "sumar"], "Libros");
}
//Funcion para generar las tablas, paso de parametros las columnas y los datos de ellas
function mostrarTablas(columnas, datos, imgPost, operaciones, nombre) {
    //Hacer thead.
    const contenedorThead = document.querySelector("#tabla > thead > tr");
    const contenedorTbody = document.querySelector("#tabla > tbody");
    const tabla = document.getElementById("tabla");
    const titulo = document.getElementById("titulo")

    titulo.innerHTML = nombre

    for (let columna of columnas) {
        const th = document.createElement("th");
        th.innerHTML = columna;
        contenedorThead.appendChild(th);
    }

    if (operaciones) {
        const th = document.createElement("th");
        th.innerHTML = "operacion";
        contenedorThead.appendChild(th);
    }

    let tieneInputs = false;
    let tieneSumar = false;
    let tieneRestar = false;
    let tieneCancelar = false;
    let tienePedido = false;

    //Hacer tbdoy
    for (let dato of datos) {
        const tr = document.createElement("tr");
        for (let posicion = 0; posicion < columnas.length; posicion++) {
            if (posicion == imgPost) {
                const img = document.createElement("img");
                img.src = dato[posicion];
                img.width = "100";
                tr.appendChild(img);
            } else {
                const td = document.createElement("td");
                td.innerHTML = dato[posicion];
                tr.appendChild(td);
            }

        }
        //Hacer operaciones y poner columnas

        if (operaciones) {
            const td = document.createElement("td");
            const input = document.createElement("input");

            for (let operacion of operaciones) {
                if (operacion == "input") {
                    tieneInputs = true;
                }
                if (operacion == "sumar") {
                    tieneSumar = true;
                }
                if (operacion == "restar") {
                    tieneRestar = true;
                }
                if (operacion == "cancelar") {
                    tieneCancelar = true;
                }
                if (operacion == "pedido") {
                    tienePedido = true;
                }
            }
            if (tieneInputs) {
                input.type = "number";
                input.className = "form-control inline"
                input.min = "0"
                td.appendChild(input);
            }
            if (tieneRestar) {
                const boton = document.createElement("button");
                boton.innerHTML = "-";
                boton.className = "btn btn-danger"
                boton.addEventListener("click", function(){eliminarLibros(dato[0], input.value)})
                td.appendChild(boton);
            }
            if (tieneSumar) {
                const boton = document.createElement("button");
                boton.innerHTML = "+";
                boton.className = "btn btn-success"
                boton.addEventListener("click", function(){añadirLibros(dato[0], input.value)})
                td.appendChild(boton);
            }
            if (tieneCancelar) {
                const boton = document.createElement("button");
                boton.innerHTML = "Cancelar";
                boton.className = "btn btn-danger"
                boton.setAttribute("onclick", "cancelarPedido("+dato[7]+")")
                td.appendChild(boton);
            }
            tr.appendChild(td);
        }

        contenedorTbody.appendChild(tr);
    }

    if (tienePedido) {
        const pedidoBoton = document.createElement("a");
        pedidoBoton.innerHTML = "Realizar pedido";
        pedidoBoton.href = "ProcesarPedido";
        tabla.appendChild(pedidoBoton);
    }
}
async function cargarGeneros() {
    const response = await fetch("Generos", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
    });
    const responsejson = await response.json();
    ocultarVistas();
    document.getElementById("principal").style.display = "block";
    vaciarTabla();
    vaciarLista()
    const ul = document.getElementById("Lista");
    for(let contador = 0; contador < responsejson.length; contador++){
        const li = document.createElement("li");
        const a = document.createElement("a");
        a.innerHTML = responsejson[contador].nombre;
        a.href = "#";
        a.setAttribute("onclick","cargarGeneroLibros('"+responsejson[contador].nombre+"')");
        li.id = responsejson[contador].cod;
        li.appendChild(a);
        ul.appendChild(li);
    }
}
async function cargarGeneroLibros(genero){
    const response = await fetch("Libros", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
    });
    vaciarLista();
    const responsejson = await response.json();
    let librosFiltrados = [] ;
    for(let libro of responsejson){
        if (libro.genero == genero) {
            librosFiltrados.push(libro)
        }
    }

    let librosArray = [];

    for (let libro of librosFiltrados) {
        const libroArray = [
            libro.isbn, libro.titulo, libro.escritores, libro.genero, libro.numpaginas, libro.imagen, libro.unidades
        ];

        librosArray.push(libroArray)
    }
    mostrarTablas(["isbn", "titulo", "escritores", "genero", "numpaginas", "imagen", "unidades"], librosArray, 5, ["input", "sumar"], "Libro del género: "+genero);
}

async function obtenerAccesos(){
    const response = await fetch("Accesos", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
    });
    const responsejson = await response.json();
    let accesosArray = [];
    for(let acceso of responsejson){
        let nuevoAcceso = [acceso.usuario, acceso.inicio, acceso.fin];
        accesosArray.push(nuevoAcceso);
    }
    vaciarTabla();
    mostrarTablas(["Usuario", "Inicio Sesión", "Finalización Sesión"], accesosArray, -1, false, "Accesos");
}

function vaciarTabla(){
    const Titulos = document.querySelectorAll("#tabla thead tr th");
    const Contenidos = document.querySelectorAll("#tabla tbody tr");
    const pedidoBoton = document.querySelector("#tabla a")
    const contenedor = document.getElementById("nArticulosUnidades")
    for(let titulo of Titulos){
        titulo.remove();
    }
    for(let contenido of Contenidos){
        contenido.remove();
    }
    if (pedidoBoton) {
        pedidoBoton.remove();
    }
    contenedor.innerHTML = ""

}
function vaciarLista(){
    const Listas = document.querySelectorAll("#Lista ul");
    const Contenidos = document.querySelectorAll("#Lista li");
    for(let lista of Listas){
        lista.remove();
    }
    for(let contenido of Contenidos){
        contenido.remove();
    }
}
async function añadirLibros(isbn, cantidad) {
    const response = await fetch("AñadirLibros", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({isbn: isbn, cantidad: cantidad})
    });

    const responsejson = await response.json();

    if (responsejson.respuesta == true) {
        alert("productos añadidos con éxito");
        cargarLibros();
    }
}
async function eliminarLibros(isbn, cantidad) {
    const response = await fetch("EliminarLibros", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({isbn: isbn, cantidad: cantidad})
    });

    const responsejson = await response.json();

    if (responsejson.respuesta == true) {
        alert("productos eliminados con éxito");
        cargarCarrito();
    }
}
async function cargarCarrito() {
    const response = await fetch("Carrito", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
    });
    const responsejson = await response.json();

    vaciarTabla();

    let carritoArray = [];

    for(let index = 1; index<responsejson.length; index++) {
        const libro = responsejson[index]
        carritoArray.push([
            libro.isbn, libro.titulo, libro.escritores, libro.genero, libro.numpaginas, libro.imagen, libro.unidades
        ]);
    }

    mostrarTablas(["isbn", "titulo", "escritores", "genero", "numpaginas", "imagen", "unidades"], carritoArray, 5, ["input", "sumar", "restar", "pedido"], "Carrito de Libros");

    //mostrar numero de unidades y artiuclos a la tabla
    const contenedor = document.getElementById("nArticulosUnidades")
    const numunidades = responsejson[0].numunidades
    const numarticulos = responsejson[0].numarticulos

    contenedor.innerHTML = "Número de Artículos: "+numarticulos+"<br><br> Número de Unidades: "+numunidades+"<br><br>"
}

async function obtenerPedidos() {
    const response = await fetch("Pedidos", {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
    });
    const responsejson = await response.json();

    vaciarTabla();

    let pedidosArray = [];

    for(let pedido of responsejson) {
        pedidosArray.push([
            pedido.usuario, pedido.isbn, pedido.titulo, pedido.genero, pedido.imagen, pedido.unidades, pedido.fechapedido, pedido.codpedido
        ]);
    }

    mostrarTablas(["Usuario", "ISBN", "Titulo", "Genero", "imagen", "unidades", "Fecha Pedido"], pedidosArray, 4, ["cancelar"], "Pedidos");

}

async function cancelarPedido(codpedido) {
    const response = await fetch("CancelarPedido", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": token,
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({codpedido: codpedido})
    });
    const responsejson = await response.json();

    if (responsejson.respuesta == true) {
        alert("Pedido cancelado con éxito");
        obtenerPedidos();

    } else {
        alert("No se ha podido cancelar el pedido");
    }
}


ComprobarInicioSesion();


