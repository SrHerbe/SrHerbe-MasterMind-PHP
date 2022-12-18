<?php
include "funciones.php";
include("./clases/Usuario.class.php");
include("./clases/DAO.class.php");

session_start();

$DAOdatos = new Dao("./csv/datos.csv");
$DAOpartidas = new Dao("./csv/partidas.csv");


//Cuando alguien entre a esta página se verificará:

//Si no existe una sesión con usuario nos mostrará un mensaje de error.
if (!isset($_SESSION["usuario"])) {
    header("location: login.php");
}
//Si intentamos entrar sin el rol de administrador nos mostrará un mensaje de error.
else if (!$DAOdatos->esAdmin($_SESSION["usuario"], ";")) {
    // die('NO TIENES PERMISOS PARA HACER ESTO');
    header("location: index.php");
}

if (isset($_GET["eliminar"])) {

    $deluser = $DAOdatos->leerFichero(";")[$_GET["eliminar"]];
    //1. Eliminamos el historial de partidas de ese usuario
    if (file_exists("./csv/partidas/".$deluser[0].".csv")) {
        unlink("./csv/partidas/".$deluser[0].".csv");
    }

    //2. Eliminamos el usuario del leaderboard

    foreach ($DAOpartidas->leerFichero(";") as $key => $value) {
        if ($key!=0) {
            if (explode(",", $value[0])[1] == $deluser[0]) {
                $DAOpartidas->remove_line("./csv/partidas.csv", $key);
            }
        }
    }

    //3. Eliminamos su foto de perfil

    if (file_exists(dirname(__FILE__) . "\img\perfil\\".$deluser[0].".jpg")) {
        unlink(dirname(__FILE__) . "\img\perfil\\".$deluser[0].".jpg");
    }
    if (file_exists(dirname(__FILE__) . "\img\perfil\\".$deluser[0].".png")) {
        unlink(dirname(__FILE__) . "\img\perfil\\".$deluser[0].".png");
    }

    //4. Eliminamos los datos del usuario

    $DAOdatos->remove_line("./csv/datos.csv", $_GET["eliminar"]);
    header("location: manageuser.php");
}

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/general.css">
    <link rel="stylesheet" href="./css/manageuser.css">
    <link rel="icon" type="image/jpg" href="./img/web_containt/logo.png" />
    <?php
    //Si la cookie estilo no está vacía estableceremos su valor como estilo
    if (!empty($_COOKIE["estilo"])) {
        if ($_COOKIE["estilo"] == "oscuro") {
            echo "<link rel=\"stylesheet\" href=\"./css/oscuro.css\">";
        }
    }
    ?>
</head>

<body>
    <header>
        <div id="header_contents" class="centrado">
            <div class="header_item" id="contenedor_logo">
                <a href="./index.php">
                    <img src="./img/web_containt/brain_gym1.png" alt="Logo">
                    <!-- <h1 id="logo_txt">MasterMind</h1> -->
                    <img id="logo" src="./img/web_containt/logo_vectorizado.svg" alt="">
                </a>
            </div>
            <div class="header_item">
                <ul class="header_item" id="menu">

                    <label for="menu_responsive"><img id="foto_perfil" src="<?php cargarImagen(); ?>" alt="Foto de Perfil"></label>
                    <button id="menu_responsive" name="menu_responsive"></button>

                    <div id="contenedor_items_responsive">
                        <li class="item_menu"><a class="item_menu" href="./index.php">Inicio</a></li>
                        <li><a class="item_menu" href="./leaderboard.php">Leaderboard</a></li>
                        <?php
                        //Si esta registrado le mostraremos botones adicionales
                        if (isset($_SESSION["usuario"])) {
                            echo "<li><a class=\"item_menu\" href=\"./dificultad.php\">Jugar</a></li>";
                            //Y si es administrador le mostraremos todos los botones de administración
                            if ($DAOdatos->esAdmin($_SESSION["usuario"], ";")) { // $DAOdatosdatos->esAdmin($_SESSION["usuario"],";")
                                echo "<li><a class=\"item_menu\" href=\"./manageuser.php\">GestUsuarios</a></li>";
                                echo "<li><a class=\"item_menu\" href=\"./managepartidas.php\">GestPartidas</a></li>";
                            }
                            echo "<li><a id=\"perfil\" class=\"item_menu\" href=\"./perfil.php\">Perfil</a></li>";
                        } else {
                            echo "<li><a class=\"item_menu\" href=\"./login.php\">Login</a></li>";
                            echo "<li><a id=\"register\" class=\"item_menu\" href=\"./register.php\">Register</a></li>";
                        }
                        ?>
                    </div>
                </ul>
                <?php
                if (isset($_SESSION["usuario"])) { ?>
                    <a id="img_perfil" href="./perfil.php"><img id="foto_perfil" src="<?php cargarImagen(); ?>" alt="Foto de Perfil"></a>
                <?php } ?>
            </div>
        </div>
    </header>

    <section id="contenedor_tabla">
            <h1>Gestión de Usuarios</h1>
            <div id="tabla_scroll">
            <table>
                <?php
                //Si el fichero datos.csv existe mostrará su contenido
                if (file_exists("./csv/datos.csv")) {
                    $DAOdatos->mostrarFichero(";", "manageuser.php");
                }
                ?>
            </table>
        </div>
    </section>

    <section id="crear_user">

        <h1>Crear Usuario</h1>
        <?php

        // Cuando cargamos el PHP generamos un token de 64 valores aleatorios [A-Z][a-z] y [0-9]
        $token = "";
        $valores = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < 64; $i++) {
            $token .= $valores[random_int(0, strlen($valores) - 1)];
        }

        // Creamos el array errores para almacenar los errores que hayan después de presionar el botón submit
        $errores = [];

        //Si hemos intentado enviar el formulario harémos las siguientes comprobaciones, si no hay ningún error almacenaremos los datos del formulario
        if (isset($_POST["enviar"])) {
            // Comprueban que los datos pedidos en el formulario no estén vacios
            if (empty($_POST["name"])) {
                $errores[] = "falta el nombre";
            } else if (validarNombre($_POST["name"])) {
                $errores[] = "Ese usuario ya existe!";
            }

            //comprueba que contraseña no este vacío, no contenga ";"
            if (empty($_POST["password"])) {
                $errores[] = "falta la contraseña";
            } else if (preg_match("/;/", $_POST["password"])) {
                $errores[] = "Formato de contraseña incorrecto";
            } else {
                $_POST["password"] = crypt($_POST["password"], '$2a$07$semillaDePruebaParaFormulario$');
            }

            //La confirmación no puede estar vacia
            if (empty($_POST["passwordConfirm"])) {
                $errores[] = "falta la confirmación de la contraseña";
            } else {
                $_POST["passwordConfirm"] = crypt($_POST["passwordConfirm"], '$2a$07$semillaDePruebaParaFormulario$');

                //y debe ser igual que la contraseña
                if ($_POST["password"] != $_POST["passwordConfirm"]) {
                    $errores[] = "Las contraseñas no coinciden";
                }
            }

            //Debe haber un rol
            if (empty($_POST["rol"])) {
                $errores[] = "falta el rol";
            }

            // Debe existir un token
            if (empty($_POST["token-csrf"])) {
                $errores[] = "Falta el token";
            }

            //Debe existir el email
            if (empty($_POST["email"])) {
                $errores[] = "falta el email";
            }
            // Debe tener un formato válido
            else if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) != true) {
                $errores[] = "Email inválido";
            } else if (validarEmail($_POST["email"])) {
                $errores[] = "Ese correo ya existe!";
            }

            //Recorremos el array de errores y lo imprimimos en caso de que haya alguno
            echo "<div id=\"mensaje\">";
            echo "<ul>";
            foreach ($errores as $key => $value) {
                echo "<li>" . $value . "</li>";
            }
            echo "</ul>";
            echo "</div>";

            //Si no hay ningún error mostraremos el mensaje de datos almacenados correctamente
            if (empty($errores)) {

                //Almacenamos los datos del usuario
                $datosUser = new Usuario($_POST["name"], $_POST["password"], $_POST["passwordConfirm"], $_POST["rol"], $_POST["email"]);

                $DAOdatos->escribirFichero($datosUser->datos(), $datosUser->keys());

                //Forzamos el refresco de la página para ver los datos modificados
        ?>
                <script>
                    window.location.replace("manageuser.php");
                </script>
        <?php
            }
        }
        ?>

        <div id="contenido">
            <form action="manageuser.php" id="formulario" method="POST">
                <label for="name">Username:</label>
                <?php //En los php de cada input guardamos el valor anterior en caso de que el formulario se recargue
                ?>
                <input type="text" name="name" id="name" value="<?php if (isset($_POST["enviar"])) {echo $_POST["name"];} ?>" placeholder="Username">

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Password">

                <label for="passwordConfirm">Confirm Password:</label>
                <input type="password" name="passwordConfirm" id="passwordConfirm" placeholder="Password">

                <select name="rol" id="rol" class="dividir">
                    <option disabled <?php if (empty($_POST["rol"])) {echo "selected";} ?>>Rol</option>
                    <option value="user" <?php if (isset($_POST["rol"]) && $_POST["rol"] == "user") {echo "selected";} ?>>Usuario</option>
                    <option value="admin" <?php if (isset($_POST["rol"]) && $_POST["rol"] == "admin") {echo "selected";} ?>>Administrador</option>
                </select>

                <input type="hidden" name="token-csrf" id="token-csrf" value="<?php echo $token ?>">

                <label for="email">Email:</label>
                <input type="text" name="email" id="email" value="<?php if (isset($_POST["enviar"])) {echo $_POST["email"];} ?>" placeholder="Email">

                <input type="submit" value="Añadir" name="enviar" id="enviar">
            </form>
        </div>
    </section>

    <section id="rol_user">
        <h1>Modificar Rol</h1>

        <?php 
            //Si pulsamos el botón actualizar
            if (isset($_POST["actualizar"])) {
                $errorupdate = [];
                $existe = false;

                //Leemos 
                foreach ($DAOdatos->leerFichero(";") as $key => $value) {
                    if ($key!=0) {
                        if ($_POST["username"] == $value[0]) {
                            $existe = true;
                            $user = $value;
                            $linea = $key;
                        }
                    }
                }

                if (!$existe) {
                    $errorupdate [] = "Ese usuario no existe!";
                }

                if (empty($_POST["rolupdate"])) {
                    $errorupdate [] = "Debes seleccionar un rol!";
                }

                if (empty($errorupdate)) {
                    $user[3] = $_POST["rolupdate"];
                    echo implode(";", $user);
                    $DAOdatos->remove_line("./csv/datos.csv", $linea);
                    $DAOdatos->escribirFichero(implode(";", $user),"name;password;passwordConfirm;rol;email;Eliminar");
                    ?>
                    <script>
                        window.location.replace("manageuser.php");
                    </script>
                    <?php
                } else {
                    echo "<ul>";
                    foreach ($errorupdate as $key => $value) {
                        echo "<li>".$value."</li>";
                    }
                    echo "</ul>";
                }
            }
        ?>


        <form action="manageuser.php" method="post" id="form_update">
            <input type="text" name="username" id="username" value="<?php if (isset($_POST["actualizar"])) {echo $_POST["username"];} ?>" placeholder="Username">
            <select name="rolupdate" id="rolupdate" class="dividir">
                <option disabled <?php if (empty($_POST["rolupdate"])) {echo "selected";} ?>>Rol</option>
                <option value="user" <?php if (isset($_POST["rolupdate"]) && $_POST["rolupdate"] == "user") {echo "selected";} ?>>Usuario</option>
                <option value="admin" <?php if (isset($_POST["rolupdate"]) && $_POST["rolupdate"] == "admin") {echo "selected";} ?>>Administrador</option>
            </select>
            <input type="submit" value="Actualizar" name="actualizar" id="actualizar">
        </form>
    </section>



    <script src="./js/cabecera.js"></script>
</body>

</html>