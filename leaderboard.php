<?php
/*
Título: Proyecto MasterMind

Autor: Samuel Hernández Berrocal

Fecha de modificación: 14/12/2022

Versión 1.0
*/
?>
<?php
include "funciones.php";
include("./clases/DAO.class.php");

session_start();

$DAOdatos = new Dao("./csv/datos.csv");
$DAOpartidas = new Dao("./csv/partidas.csv");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="./css/general.css">
    <link rel="stylesheet" href="./css/leaderboard.css">
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

    if (isset($_SESSION["usuario"])) {
        //Si la cookie tamaño no esta vacía comprobaremos si el tamaño es grande o mediano(por defecto)
        if (!empty($_COOKIE["tamano"])) {
            if ($_COOKIE["tamano"] == "grande") {
                echo "p,a,label,h1,h2,input,select {font-size: 20px !important;}";
            }
        }

        //Si la cookie fuente no está vacía estableceremos su valor como fuente
        if (!empty($_COOKIE["fuente"])) {
            echo "* {font-family:" . $_COOKIE["fuente"] . " !important;}";
        }
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

    <section id="leaderboard" class="centrado">
        <div class="leaderboard_contenedor">
            <div class="leaderboard_cabecera">
                <div>
                    <p>Username</p>
                </div>
                <div class="dificultad">
                    <p>Dificultad</p>
                </div>
                <div class="intentos">
                    <p>Intentos</p>
                </div>
                <div class="tiempo">
                    <p>Tiempo</p>
                </div>
                <div>
                    <p>Puntos</p>
                </div>
            </div>

            <div class="leaderboard_contenido">

                <?php
                $mostrar = $DAOpartidas->leerFichero(",");
                //Hay funciones que ordenan arrays bidimensionales pero como el dato del array que quiero ordenar es el primero arsort me lo ordena igualmente
                arsort($mostrar);

                // Mostrar tabla de clasificación
                foreach ($mostrar as $i => $valuei) { 
                    
                    if ($i!=0) {
                    ?>

                    <div class="leaderboard_persona">
                        <div class="leaderboard_name">
                            <img src="<?php echo cargarImagenUser($valuei[1]); ?>" alt="perfil">
                            <p><?php echo $valuei[1] ?></p>
                        </div>
                        <div class="dificultad"><?php echo $valuei[2] ?></div>
                        <div class="intentos"><?php echo $valuei[3] ?></div>
                        <div class="tiempo"><?php 
                        if ((strtotime($valuei[5]) - strtotime($valuei[4])) < 1) {
                            echo "1";
                        } else {
                            echo (strtotime($valuei[5]) - strtotime($valuei[4]))/60;
                        }
                        ?> min</div>
                        <div class="puntos"><?php echo $valuei[0] ?>pts</div>
                    </div>


                <?php
                    }    
            }
                ?>

            </div>

        </div>
    </section>

    <script src="./js/cabecera.js"></script>
</body>

</html>