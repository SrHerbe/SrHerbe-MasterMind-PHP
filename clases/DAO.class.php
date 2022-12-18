<?php
/*
Título: Tarea 1 - Unidad 4

Autor: Samuel Hernández Berrocal

Fecha de modificación: 21/11/2022

Versión 1.0
*/

class DAO
{
    private $rutaFichero;

    public function getRutaFichero(){return $this->rutaFichero;}

    public function __construct($rutaFichero) {
        $this->rutaFichero = $rutaFichero;
    }

    //Escribe los atributos de $usuario en el csv
    public function escribirFichero($datosEscribir, $cabecera) {
        //Si el fichero existe lo abrimos
        if (file_exists($this->rutaFichero)) {
            $datos = fopen($this->rutaFichero, "a+");
        }
        //Si no existe lo creamos e introduce la primera línea(cabecera) con los nombres
        else {
            $datos = fopen($this->rutaFichero, "a+");

            fwrite($datos, $cabecera);

        }

        //Introduce los datos del usuario
        fwrite($datos, $datosEscribir);

        fclose($datos);
    }
    
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

    //Lee el CSV y devuelve un Array bidimensional con sus datos
    function leerFichero($separador) {
    
        $datos = fopen($this->rutaFichero, "r+");
        $datosAlmacenados = array();
        $cont = 0;
    
        //Si el fichero datos existe
        if ($datos) {
            //El bucle se repetirá mientras sigan habíendo líneas sin leer
            while (($linea = fgets($datos, 4096)) !== false) {
                //Almacenalos la línea en un array
                $datosAlmacenados[$cont] = explode($separador, $linea);
                $cont++;
            }
        }
        fclose($datos);

        return $datosAlmacenados;
    }

    //Lee el array bidimensional y lo muestra en una tabla
    public function mostrarFichero($separador, $location) {

        $datosAlmacenados = $this->leerFichero($separador);

        //Recorremos el array
        foreach ($datosAlmacenados as $i => $value) {
            //Si es la primera fila abrimos la cabecera
            if ($i == 0) {
                echo "<thead>";
            }
            //Para el resto creamos filas
            else {
                echo "<tr>";
            }
            //Recorremos el primer valor (que es otro array)
            foreach ($datosAlmacenados[$i] as $key => $value) {
                //Si es la primera columna la abrimos y cerramos como cabecera
                if ($i == 0) {
                    echo "<th>" . $value . "</th>";
                } else if ($key == count($datosAlmacenados[$i])-1) {
                    echo "<td>" . $value . "</td>";
                    echo "<td> <a href=\"$location?eliminar=$i\">Eliminar</a> </td>";
                }
                //Para el resto la abrimos y cerramos como columna
                else {
                    echo "<td>" . $value . "</td>";
                }
            }
            //Si es el final de la primera fila cerramos la cabecera
            if ($i == 0) {
                echo "</thead>";
            }
            //Para el resto cerramos la fila
            else {
                echo "</tr>";
            }
            
        }
    }

    //Valida que el usuario, hash de contraseña y rol sea correcto
    function validarInicio($user, $hash, $separador)
    {

        //leemos el csv, en caso de no poder, mostramos mensaje de error
        if (!$datos = @fopen($this->rutaFichero, "r+")) {
            echo "<li>Hubo un error al conectarse con la base de datos.</li>";
        } else {
            $datosAlmacenados = $this->leerFichero($separador);
            $existe = false;
    
            //Recorremos el array de datos almacenados
            foreach ($datosAlmacenados as $i => $value) {
                //Si en alguna línea coincide tanto el nombre, contraseña como rol devolveremos true
                if (hash_equals($datosAlmacenados[$i][0], $user) && hash_equals($datosAlmacenados[$i][1], $hash)) {
                    $existe = true;
                }
            }
    
            return $existe;
        }
    }

    //Es admin?
    function esAdmin($user, $separador){
        //leemos el csv, en caso de no poder mostramos mensaje de error
        if (!$datos = @fopen($this->rutaFichero, "r+")) {
            echo "<li>Hubo un error al conectarse con la base de datos.</li>";
        }

        $datosAlmacenados = $this->leerFichero($separador);
        $esAdmin = false;

        //Recorremos el array de datos almacenados
        foreach ($datosAlmacenados as $i => $value) {
            //Si en alguna línea coincide tanto el nombre, contraseña como rol devolveremos true
            if (hash_equals($datosAlmacenados[$i][0], $user)) {
                if ($datosAlmacenados[$i][3]==="admin") {
                    $esAdmin = true;
                }
                
            }
        }

        return $esAdmin;
    }

}

?>