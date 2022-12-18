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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio</title>
    <link rel="stylesheet" href="./css/general.css">
    <link rel="stylesheet" href="./css/index.css">
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
    <div class="rain">
        <span style="--i:11; background-color:red; box-shadow: 0 0 50px 10px red;"></span>
        <span style="--i:12; background-color:yellow; box-shadow: 0 0 50px 10px yellow;"></span>
        <span style="--i:10; background-color:green; box-shadow: 0 0 50px 10px green;"></span>
        <span style="--i:14; background-color:blue; box-shadow: 0 0 50px 10px blue;"></span>
        <span style="--i:18; background-color:orange; box-shadow: 0 0 50px 10px orange;"></span>
        <span style="--i:16; background-color:purple; box-shadow: 0 0 50px 10px purple;"></span>
        <span style="--i:19; background-color:red; box-shadow: 0 0 50px 10px red;"></span>
        <span style="--i:20; background-color:yellow; box-shadow: 0 0 50px 10px yellow;"></span>
        <span style="--i:19; background-color:green; box-shadow: 0 0 50px 10px green;"></span>
        <span style="--i:10; background-color:blue; box-shadow: 0 0 50px 10px yellowblue;"></span>
        <span style="--i:16; background-color:orange; box-shadow: 0 0 50px 10px orange;"></span>
        <span style="--i:14; background-color:purple; box-shadow: 0 0 50px 10px purple;"></span>
        <span style="--i:18; background-color:red; box-shadow: 0 0 50px 10px red;"></span>
        <span style="--i:11; background-color:yellow; box-shadow: 0 0 50px 10px yellow;"></span>
        <span style="--i:13; background-color:green; box-shadow: 0 0 50px 10px green;"></span>
        <span style="--i:15; background-color:blue; box-shadow: 0 0 50px 10px blue;"></span>
        <span style="--i:12; background-color:orange; box-shadow: 0 0 50px 10px orange;"></span>
        <span style="--i:17; background-color:purple; box-shadow: 0 0 50px 10px purple;"></span>
        <span style="--i:13; background-color:red; box-shadow: 0 0 50px 10px red;"></span>
        <span style="--i:15; background-color:yellow; box-shadow: 0 0 50px 10px yellow;"></span>
        <span style="--i:10; background-color:green; box-shadow: 0 0 50px 10px green;"></span>
        <span style="--i:14; background-color:blue; box-shadow: 0 0 50px 10px blue;"></span>
        <span style="--i:18; background-color:orange; box-shadow: 0 0 50px 10px orange;"></span>
    </div>

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



    <section>
        <div class="presentacion centrado">
            <div class="presentacion_content">
                <div class="txt">
                    <img id="presentacion_logo" src="./img/web_containt/logo_vect_black.svg" alt="">
                    <div class="presentacion_botones">
                        <div>
                            <a id="presentacion_que_es" href="#que_es"><img id="teach_hat" src="./img/web_containt/diana.png" alt="diana">¿Qué es?</a>
                            <a id="presentacion_como_jugar" href="#como_jugar"><img id="teach_hat" src="./img/web_containt/teach_hat.png" alt="hat"> ¿Como jugar?</a>
                        </div>
                        <a id="presentacion_ir_jugar" href="./jugar.php" id="presentacion_ir_jugar"><img id="teach_hat" src="./img/web_containt/jugar_mando.png" alt="jugar"> ¡Ir a jugar!</a>
                    </div>
                </div>
                <img id="math_brain" src="./img/web_containt/math_brain.png" alt="MasterMind">
            </div>
        </div>


        <div class="separador"></div>

        <div id="contenedor_flecha">
            <a id="flecha_down" href="#que_es"><img src="./img/web_containt/flecha_down.svg" alt="flecha"></a>
        </div>
    </section>

    <section id="que_es" class="centrado">
        <div class="que_es_contenedor">
            <h1>¿Qué es MasterMind?</h1>

            <p>Mastermind es un juego de mesa, de ingenio y reflexión. El juego consiste en encontrar la combinación de fichas de colores oculta. Comenzando por la parte superior, cada fila de huecos determina un turno de la partida. En cada turno debemos arrastrar fichas de colores en todos los huecos y tocar o hacer clic en los puntos de la parte derecha para descubrir los aciertos.</p>
        </div>
    </section>

    <section id="como_jugar">
        <div class="como_jugar centrado">
                <h1>¿Cómo jugar?</h1>
                <div class="jugar_content">
                    <h3>1. Se generá el código</h3>
                    <div class="jugar_txt">
                        <p>El codificador genera un código de varias piezas, este código es una combinación de colores. Puede incluso repetir el mismo color varias veces.</p>
                    </div>
                </div>

                <div class="jugar_content">
                    <h3>2. Adivinar el código</h3>
                    <div class="jugar_txt">
                        <p>El descodificador colocará cuatro piezas en la primera fila. El codificador deberá dar pistas al descodificador. Para hacerlo, colocará una pieza blanca por cada pieza que exista en la combinación pero no este en el lugar correcto y una pieza negra si existe y está en el lugar correcto.</p>
                    </div>
                </div>

                <div class="jugar_content">
                    <h3>3. Código descifrado</h3>
                    <div class="jugar_txt">
                        <p>Si descifras el código has ganado! Intenta descifrarlo lo antes y con el menor número de intentos posibles para ganar más puntos.</p>
                    </div>
                </div>

                <div class="jugar_content">
                    <h3>4. No se encuentra el código</h3>
                    <div class="jugar_txt">
                        <p>Si se utilizan las doce filas sin lograr encontrar el código correcto el juego se da por terminado.</p>
                    </div>
                </div>
            </div>
    </section>
    <script src="./js/cabecera.js"></script>
</body>

</html>