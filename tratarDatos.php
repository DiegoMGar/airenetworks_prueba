<?php
$time_start = microtime(true);
require "helpers.php";
$mysqli = new mysqli('localhost', 'aire', 'aire', 'prueba_aire');
if ($mysqli->connect_error) {
    die('Error de Conexión (' . $mysqli->connect_errno . ') '
        . $mysqli->connect_error);
}

class Telefono
{
    public $numero;
    public $llamadas = array();
    public $conexiones = array();
    public $mediaDiariaSec = null;
    public $mediaDiariaMb = null;
    public $totalMb = 0;
    public $precioGb = 9.8;
    public $consumo = 0;
    public function __construct($n)
    {
        $this->numero = $n;
    }
    public function addLlamada($fecha, $segundos)
    {
        if (empty($this->llamadas[$fecha])) {
            $this->llamadas[$fecha] = array($segundos);
        } else {
            array_push($this->llamadas[$fecha], $segundos);
        }
    }
    public function addConexion($fecha, $mb)
    {   
        $this->totalMb += $mb;
        if (empty($this->conexiones[$fecha])) {
            $this->conexiones[$fecha] = array($mb);
        } else {
            array_push($this->conexiones[$fecha], $mb);
        }
    }
    public function calcMedia($lista)
    {
        $media = 0;
        $contador = 0;
        foreach ($lista as $k => $dia) {
            $totalSegundos = 0;
            $dias = 0;
            foreach ($dia as $segundos) {
                $totalSegundos += $segundos;
                $dias++;
            }
            $media += ($totalSegundos / $dias);
            $contador++;
        }
        if ($contador == 0) {
            return 0;
        }

        return $media / $contador;
    }
    public function calcMediaSegundos()
    {
        $this->mediaDiariaSec = round($this->calcMedia($this->llamadas),5);
        return $this->mediaDiariaSec;
    }
    public function calcMediaMb()
    {
        $this->mediaDiariaMb = round($this->calcMedia($this->conexiones),5);
        return $this->mediaDiariaMb;
    }
    public function calcConsumoGb(){
        $this->consumo = round($this->totalMb/1000*$this->precioGb,5);
        return $this->consumo;
    }
    public function cleanData(){
        unset($this->llamadas);
        unset($this->conexiones);
    }
}

$query = "select * from llamadas_a_tratar";
$telefonos = array();
$cursor = $mysqli->query($query);
while ($fila = $cursor->fetch_array(MYSQLI_ASSOC)) {
    if (empty($telefonos[$fila['origen']])) {
        $telefonos[$fila['origen']] = new Telefono($fila['origen']);
    }
    $telefonos[$fila['origen']]->addLlamada($fila['fecha'], $fila['segundos']);
}

$query = "select * from datos_a_tratar";
$cursor = $mysqli->query($query);
while ($fila = $cursor->fetch_array(MYSQLI_ASSOC)) {
    if (empty($telefonos[$fila['origen']])) {
        $telefonos[$fila['origen']] = new Telefono($fila['origen']);
    }
    $telefonos[$fila['origen']]->addConexion($fila['fecha'], $fila['mb']);
}

$query = "truncate table mediayconsumo";
$mysqli->query($query);
$queryInsert = "insert into mediayconsumo values ";
$values = array();
$arrayParaJson = array();
foreach ($telefonos as $telefono) {
    $origen = $telefono->numero;
    $sec = $telefono->calcMediaSegundos();
    $mb = $telefono->calcMediaMb();
    $consumo = $telefono->calcConsumoGb();
    $query = "(default,'{$origen}',{$sec},{$mb},{$consumo})";
    array_push($values, $query);
    $telefono->cleanData();
    array_push($arrayParaJson,$telefono);
}
$fullQuery = $queryInsert . implode(", ", $values);
if (!$mysqli->query($fullQuery) === true) {
    $time = microtime(true) - $time_start;
    $result['time'] = $time;
    $result['msg'] = 'Error en tabla mediayconsumo.';
    echo (json_encode($result));
    die();
}

$mysqli->close();
$time = microtime(true) - $time_start;
$result['time'] = $time;
$result['result'] = 'OK';
$result['telefonos'] = $arrayParaJson;
echo (json_encode($result));
