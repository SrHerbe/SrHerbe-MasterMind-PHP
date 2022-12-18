<?php 

class Partida {
    public $jugador;
    public $dificultad;
    private $intentos = 12;
    public $intentosRealizados=0;
    public $horaInicio;
    private $codigo;
    private $linea = [];
    private $tablero = [];
    private $tableroPista = [];
    private $writeLinea=0;
    public $puntuacion;
    private $status;


    public function getJugador() { return $this->jugador;}
    public function getDificultad(){ return $this->dificultad;}
    public function getIntentos(){ return $this->intentos;}
    public function getIntentosRealizados(){ return $this->intentosRealizados;}
    public function getHoraInicio() { return $this->horaInicio;}
    public function getCodigo() { return $this->codigo;}
    public function getLinea() { return $this->linea;}
    public function getTablero() {return $this->tablero;}
    public function getTableroPista() {return $this->tableroPista;}
    public function getPuntos() {return $this->puntuacion;}
    public function getStatus() {return $this->status;}



    public function __construct($jugador, $dificultad, $horaInicio){
        $this->jugador = $jugador;
        $this->dificultad = $dificultad;
        $this->horaInicio = $horaInicio;
        $this->codigo = $this->generarCode();
    }

    //Devuelve un array con el código de colores
    public function generarCode() {
        $colores = ["red", "yellow", "green", "blue", "orange", "purple"];
        $code = [];
        switch ($this->dificultad) {
            case 'facil':
                for ($i=0; $i < 4; $i++) {
                    $code [] = $colores[rand(0,5)]; 
                }
                break;
            case 'medio':
                for ($i=0; $i < 5; $i++) {
                    $code [] = $colores[rand(0,5)]; 
                }
                break;
            case 'dificil':
                for ($i=0; $i < 6; $i++) {
                    $code [] = $colores[rand(0,5)]; 
                }
                break;
        }
        $this->codigo = $code;
        return $code;
    }

    //Añade el color que el usuario ha seleccionado al array tablero
    public function addColor($color) {
        switch ($this->dificultad) {
            case 'facil':
                $num = 4;
                break;
            case 'medio':
                $num = 5;
                break;
            case 'dificil':
                $num = 6;
                break;
        }
        $this->linea[] = $color;

        $adivinado = 0;
        if (count($this->linea) == $num) {
            foreach ($this->linea as $key => $value) {
                if ($value == $this->codigo[$key]) {
                    $adivinado++;
                }
            }

            $this->tablero[$this->writeLinea] = $this->linea;
            $this->linea = [];
            $this->writeLinea++;
            $this->intentosRealizados++;
        } else {
            $this->tablero[$this->writeLinea] = $this->linea;
        }

    }

    //Devuelve true si el código es correcto y false si es incorrecto
    public function comprobarCode() {
        if ($this->tablero[count($this->tablero)-1] == $this->codigo) {
            return true;
        } else {
            return false;
        }
        
    }

    //Calcula la puntuación del usuario 
    public function calcularPuntos() {
        switch ($this->dificultad) {
            case 'facil':
                $allpts = 1000;
                break;
            case 'medio':
                $allpts = 1250;
                break;
            case 'dificil':
                $allpts = 1500;
                break;
        }
        $duracion = (strtotime(date("d-m-Y H:i"))-strtotime($this->horaInicio))/60; //Duración en minutos
        if ($duracion <1) {$duracion=1;}
        $this->puntuacion = $allpts-($this->intentosRealizados*$duracion);
    }

    //Al llamar a esta mostraremos las pistas en el array tableroPista
    public function darPistas() {
        $codAdivinar = $this->codigo;
        $codInput = $this->tablero[count($this->tablero)-1]; //Última línea del tablero
        $pistas = [];
    
        //1. Comprobamos el códigoAdivinar y añadiremos al array pistas las posiciones que están correctas
        foreach ($codAdivinar as $i => $value) {
            //Comprobamos si x posición del código Adivinar coincide con x posición del código Input
            if ($codAdivinar[$i] == $codInput[$i]) {
                $pistas [] = "black"; //Damos pista "black" ya que dicho color existe y está en la posición correcta
    
                //Descartamos los colores que ya adivinó
                unset($codAdivinar[$i]);
                unset($codInput[$i]);
            } 
        }
    
        // 2. Comprobar en el códigoAdivinar si hay alguna posición incorrecta
        foreach ($codInput as $key => $value) {
            if (in_array($value,$codAdivinar)) {
                $pistas [] = "white"; //
                unset($codAdivinar[array_search($value,$codAdivinar)]);
            }
        }
    
        if (empty($pistas)) {
            $pistas [] = "; display: none"; //
        }
    
        $this->tableroPista [] = $pistas;
    } 

    public function addStatus($parametro) {
        $this->status = $parametro;
    }
}

?>


