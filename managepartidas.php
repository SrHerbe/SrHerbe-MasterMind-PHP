<?php
include "funciones.php";
include("./clases/DAO.class.php");

session_start();

$DAOdatos = new Dao("./csv/datos.csv");
$DAOpartidas = new Dao("./csv/partidas.csv");


if (isset($_GET["eliminar"])) {
    $DAOpartidas->remove_line("./csv/partidas.csv", $_GET["eliminar"]);
    header("location: managepartidas.php");
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
    <link rel="stylesheet" href="./css/managepartidas.css">
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

    <section id="partidas" class="centrado">
        <h1>Administrar Leaderboard</h1>
        <div class="partidas_content">
            <table>
                <?php 
                $DAOpartidas->mostrarFichero(",","managepartidas.php");
                ?>
            </table>

            
        </div>
    </section>




    <script src="./js/cabecera.js"></script>
</body>

</html>