<?php
/*
Título: Proyecto MasterMind

Autor: Samuel Hernández Berrocal

Fecha de modificación: 14/12/2022

Versión 1.0
*/
?>
<?php 
include("./clases/Partida.class.php");


session_start();

$dificultad = $_SESSION["partida"]->dificultad; //Guardamos la dificultad

$_SESSION["partida"] = ""; //Reiniciamos la partida

header("location: jugar.php?dificultad=".$dificultad); //Iniciamos una partida con la misma dificultad

?>