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

//Cuando salimos de la partida 

session_start();

$_SESSION["partida"] = ""; //Reiniciamos la partida

header("location: dificultad.php"); //Redirigimos a dificultad
?>