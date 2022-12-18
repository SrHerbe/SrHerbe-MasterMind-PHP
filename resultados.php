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
include("./clases/Partida.class.php");

session_start();

$DAOdatos = new Dao("./csv/datos.csv");

if (!isset($_SESSION["usuario"])) {
    die("Para entrar aquí debes loguearte <br><br> <a href=\"./login.php\">Ir Inicio</a>");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados</title>
    <link rel="stylesheet" href="./css/general.css">
    <link rel="stylesheet" href="./css/resultados.css">
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

    <section id="contenedor_resultados" class="centrado">
        <?php 
        //Si la partida existe
        if (isset($_SESSION["partida"])) {
            // Si la partida está inicializada
            if ($_SESSION["partida"] != "") {

                //Si el estado de la partida es WIN 
                if ($_SESSION["partida"]->getStatus() == "win") {

                    //Cargamos el último dato del fichero csv partidas del usuario (que es la última partida)
                    $DAOpartida = new Dao("./csv/partidas/".$_SESSION["usuario"].".csv");
                    $estadisticas = $DAOpartida->leerFichero(",");
                    $estadisticas = $estadisticas[count($estadisticas)-1];
                    
                    //Reiniciamos la partida y mostramos las estádisticas 
                    $_SESSION["partida"] = "";
                    ?> 
                    
                    <div id="contenedor_win">
                        <h1 id="msg_win">Enhorabuena, has ganado!</h1>
                        <p class="win_item">Dificultad: <?php echo $estadisticas[2] ?></p>
                        <p class="win_item">Tiempo: <?php 
                        if (strtotime($estadisticas[5]) - strtotime($estadisticas[4]) == 0) {
                            echo 1;
                        } else {
                            echo (strtotime($estadisticas[5]) - strtotime($estadisticas[4])/60);
                        }
                        ?> min</p>
                        <p class="win_item">Intentos: <?php echo $estadisticas[3] ?></p>
                        <p class="win_item">Puntuación: <?php echo $estadisticas[0] ?>pts</p>
                        <div id="buttons">
                            <a href="./index.php">Inicio</a>
                            <a href="./dificultad.php">Volver a jugar</a>
                        </div>
                    </div>
                    
                    <?php
                } 
                //Si el status es lose 
                else if ($_SESSION["partida"]->getStatus() == "lose") {
                    $_SESSION["partida"] = ""; //Reiniciamos la partida

                    //Mostramos por pantalla que ha perdido
                    ?> 
                    <div id="contenedor_win">
                        <h1 id="msg_win">Has perdido :(</h1>
                        <p class="win_item">Suerte la próxima vez.</p>
                        <div id="buttons">
                            <a href="./index.php">Inicio</a>
                            <a href="./dificultad.php">Volver a jugar</a>
                        </div>
                    </div>
                    
                    <?php
                } 
                //Si el estado no es win ni lose significa que hay una partida en curso pero no acabada, entonces lo redirigimos a la partida para que acabe 
                else {
                    header("location: jugar.php?dificultad=".$_SESSION["partida"]->dificultad);
                }
            } 
            //Si la partida no está inicializada redirige a jugar.php
            else {
                header("location: jugar.php");
            }
        }
        ?>


    </section>

    <script src="./js/cabecera.js"></script>
</body>

</html>