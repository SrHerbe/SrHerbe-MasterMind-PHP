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
$DAOpub = new Dao("./csv/publicaciones.csv");
$DAOpubprog = new DAO("./csv/publiacionesprog.csv");

//Si el usuario no está registrado lo redirigimos al login
if (!isset($_SESSION["usuario"])) {
    header("location: login.php");
} 

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dificultad</title>
    <link rel="stylesheet" href="./css/general.css">
    <link rel="stylesheet" href="./css/dificultad.css">
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

    <section class="dificultad centrado">
        <h1>Selecciona la dificultad de la partida:</h1>
        <ul id="botones">
            <li><a id="facil" href="jugar.php?dificultad=facil">Fácil</a></li>
            <li><a id="medio" href="jugar.php?dificultad=medio">Medio</a></li>
            <li><a id="dificil" href="jugar.php?dificultad=dificil">Difícil</a></li>
        </ul>
    </section>

    <script src="./js/cabecera.js"></script>
</body>

</html>