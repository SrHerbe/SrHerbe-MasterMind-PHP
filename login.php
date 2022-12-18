<?php
/*
Título: Tarea 1 - Unidad 4

Autor: Samuel Hernández Berrocal

Fecha de modificación: 21/11/2022

Versión 1.0
*/
?>
<?php 
    include("funciones.php");
    include("./clases/DAO.class.php");

    session_start();

    $DAO = new Dao("./csv/datos.csv");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/general.css">
    <link rel="stylesheet" href="./css/login.css">
    <link rel="icon" type="image/jpg" href="./img/web_containt/logo.png" />
    <?php 
        //Si la cookie estilo no está vacía estableceremos su valor como estilo
        if (!empty($_COOKIE["estilo"])) {
            if ($_COOKIE["estilo"] == "oscuro") {echo "<link rel=\"stylesheet\" href=\"./css/oscuro.css\">";}
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

    if (isset($_POST["entrar"])) {
        //Comprobaciones
        $errores = [];

        //Si hemos intentado enviar el formulario harémos las siguientes comprobaciones, si no hay ningún error almacenaremos los datos del formulario
        if (isset($_POST["entrar"])) {
            
            if (isset($_POST["user"])) {
                $_POST["user"] = trim($_POST["user"]);
            }
            
            // Comprueba que el usuario no este vacío y comprueba su formato
            if (empty($_POST["user"])) {
                $errores[] = "- Falta el nombre.";
            }

            //Comprueba que la contraseña no este vacía, su formato y encripta la contraseña
            if (empty($_POST["password"])) {
                $errores[] = "- Falta la contraseña.";
            } else if (preg_match("/;/", $_POST["password"])) {
                $errores[] = "- Formato de contraseña inválido.";
            } else {
                $_POST["password"] = crypt($_POST["password"], '$2a$07$semillaDePruebaParaFormulario$');
            }

            echo "<ul id=\"errores\" class=\"centrado\">";
            //Recorremos el array de errores y lo imprimimos en caso de que haya alguno
            foreach ($errores as $key => $value) {
                echo "<li>" . $value . "</li>";
            }

            //Si la función validar inicio encuentra algún usuario que coincida con el nombre y 
            //contraseña que hemos introducido le redirigirá a una página u otra, dependiendo de su rol       
            if ($DAO->validarInicio($_POST["user"], $_POST["password"],";")) {
                $_SESSION["usuario"] = $_POST["user"];
                $_SESSION["rol"] = "admin";

                //si la cookie login+usuario existe le añadimos la fecha y hora actual
                if (isset($_COOKIE["datelogin" . $_POST["user"]])) {
                    $cookielogin = $_COOKIE["datelogin" . $_POST["user"]];
                    setcookie("datelogin" . $_POST["user"], $_COOKIE["datelogin" . $_POST["user"]] . ";" . time(), time() + 86400 * 30);
                }
                //Si la cookie no existe la creamos y añadimos la fecha y hora actual
                else {
                    setcookie("datelogin" . $_POST["user"], time(), time() + 86400 * 30);
                }
                //Redireccionamos al panel de administración de usuarios porque el rol de este usuario es "admin"
                header("Location: index.php");
                die();
            }
            //En caso de que no podamos validar el usuario lanzaremos error de credenciales incorrectos.
            else {
                echo "<li>- Usuario o contraseña incorrecto.</li>";
            }
            echo "</ul>";
        }
    }
    ?>
    <section id="apartado_login" class="centrado">
        <div>
            <h1>Login</h1>
            <form action="login.php" method="post">
                <div class="item">
                    <label for="user">Usuario</label>
                    <input type="text" name="user" id="user" placeholder="Usuario" value="<?php 
                    //Volvemos a mostrar el campo usuario si existe y si tiene el formato correcto porque sino podrian escribir código html
                    if (isset($_POST['user']) && preg_match("/^(?=.{3,18}$)[a-zñáéíóúA-ZÑÁÉÍÓÚ]*$/", $_POST["user"])) {echo $_POST['user'];} 
                    ?>">
                </div>

                <div class="item">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password" placeholder="Contraseña">
                </div>
                
                <span id="centrar_boton">
                    <input type="submit" value="Entrar" name="entrar" id="enviar">
                </span>
                
            </form>
        </div>
    </section>


    <script src="./js/cabecera.js"></script>
</body>

</html>