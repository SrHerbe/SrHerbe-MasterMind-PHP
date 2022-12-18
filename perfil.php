<?php
/*
Título: Proyecto MasterMind

Autor: Samuel Hernández Berrocal

Fecha de modificación: 14/12/2022

Versión 1.0
*/
?>

<?php
session_start();
//Si no existe una sesión lo redirigimos al login.
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
}

include("funciones.php");
include("./clases/DAO.class.php");

$DAOdatos = new DAO("./csv/datos.csv");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="./css/general.css">
    <link rel="stylesheet" href="./css/perfil.css">
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

    
    
    <div id="error">
        <?php
        //Si le damos al botón actualizar llamaremos a la función actualizar Imagen
        if (isset($_POST["actualizar"])) {
            actualizarImagen();
        }
        ?>

    </div>
    <div id="contenedor_saludo">
        <h1 id="saludo" class="centrado">Hola <?php echo $_SESSION["usuario"] ?></h1>
    </div>

    <section id="actualizar_foto" class="centrado">
    <label for="subirfoto" id="label_subirfoto">
        <img src="./img/web_containt/upload.png" alt="upload" id="image_upload">
        <img id="perfil_upload_image" src="<?php cargarImagen(); ?>" alt="Foto de Perfil">
    </label>
    

        <form action="perfil.php" method="POST" enctype="multipart/form-data">
            <h2>FOTO DE PERFIL</h2>
            <input type="file" name="subirfoto" id="subirfoto">
            <input class="enviar" type="submit" value="Actualizar" name="actualizar">
        </form>
    </section>

    <div id="error2" class="centrado">
        <?php 
            //Si POST modificar existe verificaremos que todos los campos contengan datos
            if (isset($_POST["modificar"])) {
                $errores = [];
                if (empty($_POST["estilo"])) {
                    $errores[]= "Debes marcar un estilo.";
                } 

                if (empty($_POST["tamano"])) {
                    $errores[]= "Debes seleccionar un tamaño.";
                }

                if (empty($_POST["fuente"])) {
                    $errores[]= "Debes seleccionar una fuente.";
                }

                //Mostramos los errores en caso de haber
                echo "<ul>";
                foreach ($errores as $key => $value) {
                    echo "<li>".$value."</li>";
                }
                echo "</ul>";

                //Si no hay ningún errror
                if (empty($errores)) {
                    //Almacenamos la cookie estilo
                    setcookie("estilo",$_POST["estilo"],time()+86400*30);

                    //Almacenamos la cookie tamaño
                    setcookie("tamano",$_POST["tamano"],time()+86400*30);

                    //Almacenamos la cookie fuente
                    setcookie("fuente",$_POST["fuente"],time()+86400*30);

                    header("location: perfil.php");
                }
            }
        ?>
    </div>


    <section id="preferencias" class="centrado">
        <form action="perfil.php" method="POST" enctype="multipart/form-data">
            <h2>PREFERENCIAS</h2>
            <label for="estilo">Estilo</label>
            <select name="estilo" id="estilo">
                <option disabled <?php if (empty($_POST["estilo"])) {echo "selected";} ?>>seleccionar tema</option>
                <option value="claro" <?php 
                //Si es la primera vez que rellenamos el formulario y lo enviamos incompleto los valores no se borrarán. Y si ya lo habíamos envíado alguna vez
                //los valores serán los que teníamos guardados en las cookies
                if (isset($_POST["estilo"]) && $_POST["estilo"]=="claro" || isset($_COOKIE["estilo"]) && $_COOKIE["estilo"]=="claro") {echo "selected";} ?>>Claro</option>
                <option value="oscuro" <?php if (isset($_POST["estilo"]) && $_POST["estilo"]=="oscuro" || isset($_COOKIE["estilo"]) && $_COOKIE["estilo"]=="oscuro") {echo "selected";} ?>>Oscuro</option>
            </select>

            <label for="tamano">Tamaño</label>
            <select name="tamano" id="tamano">
                <option disabled <?php if (empty($_POST["tamano"])) {echo "selected";} ?>>seleccionar tamaño</option>
                <option value="mediano" <?php if (isset($_POST["tamano"]) && $_POST["tamano"]=="mediano" || isset($_COOKIE["tamano"]) && $_COOKIE["tamano"]=="mediano") {echo "selected";} ?>>Mediano</option>
                <option value="grande" <?php if (isset($_POST["tamano"]) && $_POST["tamano"]=="grande" || isset($_COOKIE["tamano"]) && $_COOKIE["tamano"]=="grande") {echo "selected";} ?>>Grande</option>
            </select>

            <label for="fuente">Fuente</label>
            <select name="fuente" id="fuente">
                <option disabled <?php if (empty($_POST["fuente"])) {echo "selected";} ?>>seleccionar fuente</option>
                <option value="arial" <?php if (isset($_POST["fuente"]) && $_POST["fuente"]=="arial" || isset($_COOKIE["fuente"]) && $_COOKIE["fuente"]=="arial") {echo "selected";} ?>>Arial</option>
                <option value="Courier" <?php if (isset($_POST["fuente"]) && $_POST["fuente"]=="Courier" || isset($_COOKIE["fuente"]) && $_COOKIE["fuente"]=="Courier") {echo "selected";} ?>>Courier</option>
            </select>

            <input class="enviar" type="submit" value="Modificar" name="modificar">
        </form>
    </section>

    <section id="control_acceso" class="centrado">
        <h1>Control de acceso</h1>
        <?php 

            //Si la cookie datelogin+usuario existe
            if (isset($_COOKIE["datelogin".$_SESSION["usuario"]])) {
                //La añadiremos en un array
                $arraycookie = explode(";",$_COOKIE["datelogin".$_SESSION["usuario"]]);

                //Y lo mostraremos en formato d-m-Y H:i
                foreach ($arraycookie as $key => $value) {
                    echo "<p>".date("d-m-Y H:i", $value)."</p>";
                }
            }
        ?>
    </section>

    <section id="exit" class="centrado">
        <a href="./cerrarsesion.php">Cerrar sesión</a>
    </section>

    <script src="./js/cabecera.js"></script>
</body>

</html>