<!DOCTYPE html>
<html>

<head>
    <title>Formulario de login</title>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script type="text/javascript" src="{{ asset('cargarDatos.js') }}"></script>
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
</head>

<body>
    <section id="login">
        <form action="" method="POST" class="form form-light">
            <h5 class="font-weight-normal">Usuario</h5> <input class="form-control w-25" id="usuario" type="text"><br>
            <h5 class="font-weight-normal">Clave</h5> <input class="form-control w-25" id="clave" type="password"><br>
            <input type="button" class="btn btn-primary" value="Iniciar sesión" onclick="login();">
        </form>
    </section>

    <section id="principal" style="display:none">
        {{view("Cabecera")}}
        <hr>
        <h2 id="titulo"></h2>
        <section id="contenido">
            <div id="nArticulosUnidades"></div>
            <table class="table table-hover" id="tabla">
                <thead>
                    <tr></tr>
                </thead>
                <tbody></tbody>
            </table>
            <ul id = "Lista">

            </ul>
        </section>
    </section>
</body>

</html>
