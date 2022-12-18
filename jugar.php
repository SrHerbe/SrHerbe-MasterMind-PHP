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
$DAOpartida = new Dao("./csv/partidas/".$_SESSION["usuario"].".csv");
$DAOpartidas = new Dao("./csv/partidas.csv");

//Si el usuario no está logueado lo redirigimos al login
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
    <title>Inicio</title>
    <link rel="stylesheet" href="./css/general.css">
    <link rel="stylesheet" href="./css/jugar.css">
    <link rel="icon" type="image/jpg" href="./img/web_containt/logo.png" />
    <?php 
    // Responsive
    if ($_GET["dificultad"] == "facil" ) { echo "<link rel=\"stylesheet\" href=\"./css/jugar_facil.css\">";}
    if ($_GET["dificultad"] == "medio" ) { echo "<link rel=\"stylesheet\" href=\"./css/jugar_medio.css\">";}
    if ($_GET["dificultad"] == "dificil" ) { echo "<link rel=\"stylesheet\" href=\"./css/jugar_dificil.css\">";}
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
                            if ($DAOdatos->esAdmin($_SESSION["usuario"], ";")) {
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
    //Si ya hay una partida creada 
    if (@$_SESSION["partida"]) {
        // Redirigimos a partidaEnCurso donde nos permitirá reanudar la partida
        if ($_GET["dificultad"] != $_SESSION["partida"]->dificultad) {
            header("location: partidaEnCurso.php");
        }
        //Si desde que dejó la partida pasó más de una hora la partida se reiniciará automaticamente
        if (strtotime(date("d-m-Y H:i")) - strtotime($_SESSION["partida"]->horaInicio) > 3600) {
            $_SESSION["partida"] = "";
        }
    }
    ?>

    <section class="options">
        <a id="reiniciar" href="reiniciar.php">Reset</a>
        <a id="salir" href="salirpartida.php">Exit</a>
    </section>



    <section id="jugar" class="centrado">
        <?php 
        //A través del método GET indicamos la dificultad de la partida, dependiendo de esta se creará una partida u otra
        if (isset($_GET["dificultad"])) {
            switch ($_GET["dificultad"]) {
                case 'facil':
                    $partida = new Partida($_SESSION["usuario"], $_GET["dificultad"], date("d-m-Y H:i"));
                    break;
                case 'medio':
                    $partida = new Partida($_SESSION["usuario"], $_GET["dificultad"], date("d-m-Y H:i"));
                    break;
                case 'dificil':
                    $partida = new Partida($_SESSION["usuario"], $_GET["dificultad"], date("d-m-Y H:i"));
                    break;
                default:
                    header("location: dificultad.php");
                    break;
            }

            //Si la sesión es nueva Asignaremos un código nuevo de colores
            if (@$_SESSION["partida"] == "" || !isset($_SESSION["partida"])) {
                $_SESSION["partida"] = $partida;
            }

            // Si la partida existe comienza el juego
            if (isset($partida)) {
                ?>
                <div class="formulario_contenedor">
                    <form id="formulario" action="jugar.php?dificultad=<?php echo $partida->getDificultad();?>" method="post">
                        <input type="submit" value="" name="rojo">
                        <input type="submit" value="" name="amarillo">
                        <input type="submit" value="" name="verde">
                        <input type="submit" value="" name="azul">
                        <input type="submit" value="" name="naranja">
                        <input type="submit" value="" name="morado">
                    </form>
                </div>

                <?php
                $addcolor;

                if (isset($_POST["rojo"])) {
                    $addcolor = "red";
                }
                if (isset($_POST["amarillo"])) {
                    $addcolor = "yellow";
                }
                if (isset($_POST["verde"])) {
                    $addcolor = "green";
                }
                if (isset($_POST["azul"])) {
                    $addcolor = "blue";
                }
                if (isset($_POST["naranja"])) {
                    $addcolor = "orange";
                }
                if (isset($_POST["morado"])) {
                    $addcolor = "purple";
                }
                if (!empty($addcolor)) {
                    //Si el usuario aún no ha agotado sus intentos
                    if ($_SESSION["partida"]->getIntentosRealizados() < $_SESSION["partida"]->getIntentos()) {

                        $_SESSION["partida"]->addColor($addcolor); //Añadimos el color
                    
                        //Si la nueva línea esta vacía significa que hemos acabado la anterior
                        if (count($_SESSION["partida"]->getLinea())==0) {
                            //Ya que el usuario ha acabdo de introducir la fila vamos a comprobar si es correcta
                            if ($_SESSION["partida"]->comprobarCode()) {
                                //El código es correcto, aquí la partida ha finalizado.
                                // 1. Calculamos los puntos ganados 
                                $_SESSION["partida"]->calcularPuntos();
                                // 2. Almacenamos la partida en un CSV personal
                                    $cabecera="puntuacion,";
                                    $almacenar=$_SESSION["partida"]->puntuacion.",";
                                    //Almacenamos los datos de la partida junto con su cabecera 
                                    foreach ($_SESSION["partida"] as $key => $value) {
                                        if ($_SESSION["partida"]->puntuacion != $value) {
                                            $cabecera = $cabecera.$key.",";
                                            $almacenar = $almacenar.$value.",";
                                        }
                                    }
                                    $almacenar=$almacenar.date("d-m-Y H:i")."\n";
                                    $cabecera = $cabecera."horaFin\n";

                                    //Almacenamos la partida en un fichero CSV personal del usuario
                                    $DAOpartida->escribirFichero($almacenar, $cabecera);

                                    //Si es la mejor partida del usuario la añadimos al leaderboard
                                    foreach ($DAOpartidas->leerFichero(",") as $key => $value) {
                                        if ($value[1] == $_SESSION["usuario"]) {
                                            $puntajeAlmacenado = $value[0];
                                            $linea = $key;
                                        }
                                    }
                                    if ($puntajeAlmacenado < $_SESSION["partida"]->puntuacion) {
                                        if (isset($linea)) {
                                            $DAOpartidas->remove_line("./csv/partidas.csv",$linea);
                                        }
                                        $DAOpartidas->escribirFichero($almacenar, $cabecera);
                                    }

                                // 3. Reiniciamos la SESSION[partida]

                                echo $_SESSION["partida"]->addStatus("win");

                                // 4. Lo redireccionamos a resultados.php
                                header("location: resultados.php");
                            } 
                            //Si el usuario ha agotado sus intentos
                            else if ($_SESSION["partida"]->getIntentosRealizados() == $_SESSION["partida"]->getIntentos()) {
                                // 1. Reiniciamos la partida desde SESSION[partida]
                                echo $_SESSION["partida"]->addStatus("lose");
                                // 2. Lo redireccionamos a resultados.php
                                header("location: resultados.php");
                            } 
                            //El usuario no ha adivinado la combinación. 
                            else {
                                //Dar pistas
                                $_SESSION["partida"]->darPistas();
                            }
                        }
                    } 
                }

            } else {
                echo "no existe";
            }
        } else {
            header("location: dificultad.php");
        }

        ?>


        <div class="tablero_vacio">
            <div class="vacio_contenido">
                <?php 
                //Dependiendo de la dificultad mostraremos un tablero con más o menos bolas
                switch ($_SESSION["partida"]->dificultad) {
                    case 'facil':
                        $repetir = 4;
                        break;
                    case 'medio':
                        $repetir = 5;
                        break;
                    case 'dificil':
                        $repetir = 6;
                        break;
                }


                //Siempre 12 fils y las columnas dependerán de la dificultad
                for ($i=0; $i < 12; $i++) { ?>

                <div class="vacio">
                    <?php for ($j=0; $j < $repetir; $j++) { ?>
                    <div class="item_vacio"></div>
                    <?php } ?>
                </div>

                <?php } ?>
            </div>
        </div>


        <div class="tablero">
            <div class="tablero_column">
                <?php 
                //Mostrar fichas del usuario, simplemente imprimimos el tablero de la clase partida
                $tablero = $_SESSION["partida"]->getTablero();
                foreach ($tablero as $i => $valueI) {
                    echo "<div class=\"tablero_linea\">";
                    foreach ($tablero[$i] as $j => $valueJ) {
                        echo "<div class=\"tablero_input\" style=\"background-color:$valueJ;\">"."</div>";
                    }
                    echo "</div>";
                }
                ?>
            </div>

            <div class="tablero_column" id="column2">
            <?php 
                //Mostrar pistas del usuario
                $tablero = $_SESSION["partida"]->getTableroPista();
                foreach ($tablero as $i => $valueI) {
                    echo "<div class=\"pista_linea\">";
                    foreach ($tablero[$i] as $j => $valueJ) {
                        echo "<div class=\"tablero_pista\" style=\"background-color:$valueJ;\">"."</div>";
                    }
                    echo "</div>";
                }
                ?>
            </div>


        </div>

    </section>
    <script src="./js/jugar.js"></script>
    <script src="./js/cabecera.js"></script>
</body>

</html>