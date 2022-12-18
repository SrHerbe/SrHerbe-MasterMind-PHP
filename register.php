<?php
/*
Título: Proyecto MasterMind

Autor: Samuel Hernández Berrocal

Fecha de modificación: 14/12/2022

Versión 1.0
*/
?>

<?php
    //Añadir fichero con funciones
    include("funciones.php");
    include("./clases/Usuario.class.php");
    include("./clases/DAO.class.php");

    $DAO = new DAO("./csv/datos.csv"); 

    //Cuando alguien entre a esta página se verificará:
    session_start();
    //Si no existe una sesión con usuario nos mostrará un mensaje de error.
    if (isset($_SESSION["usuario"])) {
        header("location: index.php");
    } 
    
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./css/general.css">
    <link rel="stylesheet" href="./css/register.css">
    <link rel="icon" type="image/jpg" href="./img/web_containt/logo.png" />
    <?php 
        //Si la cookie estilo no está vacía estableceremos su valor como estilo
        if (!empty($_COOKIE["estilo"])) {
            if ($_COOKIE["estilo"] == "oscuro") {echo "<link rel=\"stylesheet\" href=\"./css/oscuro.css\">";}
        }
    ?>
</head>
<style>
    <?php 
    //Si la cookie tamaño no esta vacía comprobaremos si el tamaño es grande o mediano(por defecto)
    if (!empty($_COOKIE["tamano"])) {
        if ($_COOKIE["tamano"]=="grande") {
            echo "p,a,label,h1,h2,input,select {font-size: 20px !important;}";
        }
    }
    ?>
    <?php 
    //Si la cookie fuente no está vacía estableceremos su valor como fuente
    if (!empty($_COOKIE["fuente"])) {
        echo "* {font-family:".$_COOKIE["fuente"]." !important;}";
    }
    ?>
</style>

<body>
<header>
        <div id="header_contents" class="centrado">
            <div class="header_item" id="contenedor_logo">
                <a href="./index.php">
                    <img src="./img/web_containt/brain_gym1.png" alt="Logo">
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
                            if ($DAOdatos->esAdmin($_SESSION["usuario"], ";")) { // $DAOdatos->esAdmin($_SESSION["usuario"],";")
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



            if (!empty($_POST["name"])) {
                $_POST["name"] = trim($_POST["name"]);
            }


            // Comprueban que los datos pedidos en el formulario no estén vacios
            if (empty($_POST["name"])) {
                $errores[] = "falta el nombre";
            } else if (!preg_match("/^(?=.{3,18}$)[a-zñáéíóúA-ZÑÁÉÍÓÚ]*$/", $_POST["name"])) {
                $errores[] = "Formato de nombre inválido";
            } else if (validarNombre($_POST["name"])) {
                $errores[] = "Ese usuario ya existe!";
            }

            //comprueba que contraseña no este vacío, no contenga ";", tenga una mayúscula, una minúscula, un caracter, un número y de 8 a 16 caracteres
            if (empty($_POST["password"])) {
                $errores[] = "falta la contraseña";
            } else if (preg_match("/;/", $_POST["password"])) {
                $errores[] = "Formato de contraseña incorrecto";
            } else if (!preg_match("/[*~@-_,.+:\/*=]+/", $_POST["password"])) {
                $errores[] = "La contraseña debe incluir un carácter caracter especial (~@-_+:/*=)";
            } else if (!preg_match("/^(?=[a-zA-Z0-9~@\-_,.+:\/*=]*\d)(?=[a-zA-Z0-9~@\-_,.+:\/*=]*[A-Z])(?=[a-zA-Z0-9~@\-_,.+:\/*=]*[a-z])(?=[a-zA-Z0-9~@\-_,.+:\/*=]*[*~@-_+:\/*=]+)\S{8,16}$/", $_POST["password"])) {
                $errores[] = "La contraseña debe tener entre 8 y 16 caracteres, al menos un dígito, una minúscula, una mayúscula y un caracter especial.";
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
            echo "<div id=\"mensaje\" class=\"centrado\">";
            echo "<ul>";
            foreach ($errores as $key => $value) {
                echo "<li>" . $value . "</li>";
            }
            echo "</ul>";
            echo "</div>";

            //Si no hay ningún error mostraremos el mensaje de datos almacenados correctamente
            if (empty($errores)) {
                header("location: login.php");
                
                //Almacenamos los datos del usuario
                $datosUser = new Usuario($_POST["name"], $_POST["password"], $_POST["passwordConfirm"], "user", $_POST["email"]);

                $DAO -> escribirFichero($datosUser->datos(), $datosUser->keys());
            }
        }
        ?>


    <section id="apartado_register" class="centrado">
        <div>
            <form action="register.php" id="formulario" method="POST">
                <label for="name">Username:</label>
                <?php //En los php de cada input guardamos el valor anterior en caso de que el formulario se recargue?>
                <input type="text" name="name" id="name" value="<?php if (isset($_POST["enviar"])) {echo $_POST["name"];} ?>">

                <label for="password">Password:</label>
                <input type="password" name="password" id="password">

                <label for="passwordConfirm">Confirm Password:</label>
                <input type="password" name="passwordConfirm" id="passwordConfirm">

                <input type="hidden" name="token-csrf" id="token-csrf" value="<?php echo $token ?>">

                <label for="email">Email:</label>
                <input type="text" name="email" id="email" value="<?php if (isset($_POST["enviar"])) {echo $_POST["email"];} ?>">

                <input type="submit" value="Enviar" name="enviar" id="enviar">
            </form>
        </div>
    </section>



    <script src="./js/cabecera.js"></script>
</body>

</html>