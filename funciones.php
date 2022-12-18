<?php
/*
Título: Proyecto MasterMind

Autor: Samuel Hernández Berrocal

Fecha de modificación: 14/12/2022

Versión 1.0
*/
?>
<?php

//Elimina la línea indicada en el CSV
function remove_line($file,$n){
    //leemos el archivo 
    $handle = fopen($file, "r");
    // almacenara la data
    $result = "";
    // contador de lineas
    $i =0;
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            // aumentamos en 1 la linea
            $i++;
            // removemos espacios de mas
            $line = trim(preg_replace('/\s+/', ' ', $line));
            // validamos que sea una linea en blanco o el numero de linea especificado y saltamos a la siguiente interacion
            if ( $line == "" || $n == ($i -1)) continue;

            // almacenamos los resultados
            $result .= $line."\n";  
        }
        // cerramos el archivo
        fclose($handle);
    } else {
        die("No se pudo abrir el arcivo {$file};");
    }

    // re abrimos en modo escritura
    $handle = fopen($file, "w+");
    // escribimos la nueva data
    fwrite($handle, $result);
    // cerramos el archivo
    fclose($handle);
}

//Comprueba el párametro $user e indica si ese usuario ya existe o no
function validarNombre($user) {
    //leemos el csv, en caso de no poder mostramos mensaje de error
    if (!$datos = @fopen("./csv/datos.csv", "r+")) {
        return false;
    }

    $datosAlmacenados = array();
    $cont = 0;
    $existe = false;

    if (isset($_GET["eliminar"])) {
        remove_line("datos.csv", $_GET["eliminar"]);
    }

    //Si el fichero datos existe
    if ($datos) {
        //El bucle se repetirá mientras sigan habíendo líneas sin leer
        while (($linea = fgets($datos, 4096)) !== false) {
            //Almacenalos la línea en un array
            $datosAlmacenados[$cont] = explode(";", $linea);
            $cont++;
        }
    }

    //recorremos el array de datos almacenados
    foreach ($datosAlmacenados as $i => $value) {
        //Si en alguna línea existe el nombre que hemos pasado como parámetro devolveremos true
        if (hash_equals($datosAlmacenados[$i][0], $user)) {
            $existe = true;
        }
    }

    return $existe;
}

//Comprueba el párametro $user e indica si ese usuario ya existe o no
function validarEmail($email) {
    //leemos el csv, en caso de no poder mostramos mensaje de error
    if (!$datos = @fopen("./csv/datos.csv", "r+")) {
        return false;
    }

    $datosAlmacenados = array();
    $cont = 0;
    $existe = false;

    if (isset($_GET["eliminar"])) {
        remove_line("./csv/datos.csv", $_GET["eliminar"]);
    }

    //Si el fichero datos existe
    if ($datos) {
        //El bucle se repetirá mientras sigan habíendo líneas sin leer
        while (($linea = fgets($datos, 4096)) !== false) {
            //Almacenalos la línea en un array
            $datosAlmacenados[$cont] = explode(";", $linea);
            $cont++;
        }
    }
    // echo "Email: ". $email;
    //recorremos el array de datos almacenados
    foreach ($datosAlmacenados as $i => $value) {
        //Si en alguna línea existe el nombre que hemos pasado como parámetro devolveremos true
        if (hash_equals(trim($datosAlmacenados[$i][4]), trim($email))) {
            $existe = true;
        }
    }
    return $existe;
}

//Imprime la Imagen de perfil que se debe llamar como nuestro nombre de usuario
function cargarImagen(){

    if (!isset($_SESSION["usuario"])) {
        echo "./img/perfil/default.png";
    } 
    //Si la foto en formato png existe la mostramos
    else if (file_exists("./img/perfil/" . $_SESSION["usuario"] . ".png")) {
        echo "./img/perfil/" . $_SESSION["usuario"] . ".png";
    } 
    //Sino comprobamos si existe en formato jpg
    else if (file_exists("./img/perfil/" . $_SESSION["usuario"] . ".jpg")) {
        echo "./img/perfil/" . $_SESSION["usuario"] . ".jpg";
    } 
    //Sino mostraremos la foto de perfil por defecto en caso de que exista
    else if (file_exists("./img/perfil/default.png")) {
        echo "./img/perfil/default.png";
    }
}

//Imprime la Imagen de perfil que se debe llamar como nuestro nombre de usuario
function cargarImagenUser($username){

    //Si la foto en formato png existe la mostramos
    if (file_exists("./img/perfil/" . $username . ".png")) {
        echo "./img/perfil/" . $username . ".png";
    } 
    //Sino comprobamos si existe en formato jpg
    else if (file_exists("./img/perfil/" . $username . ".jpg")) {
        echo "./img/perfil/" . $username . ".jpg";
    } 
    //Sino mostraremos la foto de perfil por defecto en caso de que exista
    else if (file_exists("./img/perfil/default.png")) {
        echo "./img/perfil/default.png";
    }
}

//Borra todo lo que no sea png o jpg de la carpeta imagenes para evitar algún posible script RFI que Aarón quiera meter
function secureUpload($afteractual)
{
    $actual = dirname(__FILE__);
    $borrar = glob($actual . $afteractual);

    foreach ($borrar as $i => $value) {
        if (!preg_match("/^(.*(\.png|\.jpg)$)/",$value)) {
            unlink($value);
        }
    }
}

//Actualiza la foto de perfil del usuario
function actualizarImagen()
{
    //Si el documento enviado no existe mostrará un mensaje de error
    if (!$_FILES["subirfoto"]["name"]) {
        echo "<p>Debes subir una foto para poderla actualizar.</p>";
    } 
    //Si existe
    else {
        $nombre = $_FILES["subirfoto"]["name"];
        $file_type = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        $rutaTemp = $_FILES["subirfoto"]["tmp_name"];
        $rutaDestino = dirname(__FILE__) . "\img\perfil";
        $rutaCompleta = $rutaDestino . "\\" . $_SESSION["usuario"] . ".";


        //Comprobaremos la extensión para que solo sea png o jpg
        if ($file_type == "png" || $file_type == "jpg") {

            //Si el contenedor de imágenes no existe lo creamos
            if (!file_exists($rutaDestino)) {
                mkdir($rutaDestino, 0777, true);
            }

            //Dependiendo del tipo de archivo subido
            switch ($file_type) {
                case "png":
                    //Si es png borramos un posible jpg en caso de que exista para que no hayan fotos duplicadas con distintas extensiones 
                    if (file_exists($rutaCompleta . "jpg")) {
                        unlink($rutaCompleta . "jpg");
                    }
                    break;
                case "jpg":
                    //Si es jpg borramos un posible png en caso de que exista para que no hayan fotos duplicadas con distintas extensiones
                    if (file_exists($rutaCompleta . "png")) {
                        unlink($rutaCompleta . "png");
                    }
                    break;
            }

            //Si podemos mover la foto de la ruta temporal a nuestro contenedor 
            if (move_uploaded_file($rutaTemp, $rutaCompleta . $file_type)) {
                //Esta función intenta evitar posible RFI
                secureUpload("/imagenes/*.*");
                //Refrescamos la página para que el usuario ya vea la foto de perfil cambiada
                header("location: perfil.php");
            } 
            //Sino mostramos un mensaje de error
            else {
                echo "<p>Ha ocurrido un error en la subida</p>";
            }
        } 
        //Si no es jpg o png mostramos mensaje de error
        else {
            echo "<p>La foto debe ser jpg o png.</p>";
        }
    }

    //Volvemos a verificar que no haya RFI
    secureUpload("/imagenes/*.*");
}

//Verifica que la imagen de un input file sea jpg o png
function verificarImg(){
    $nombre = $_FILES["multimedia"]["name"];
    $file_type = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

    //Comprobaremos la extensión para que solo sea png o jpg
    if ($file_type == "png" || $file_type == "jpg") {
        return true;
    } else {
        return false;
    }
}
