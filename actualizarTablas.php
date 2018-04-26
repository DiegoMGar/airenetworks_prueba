<?php
$time_start = microtime(true);
require "helpers.php";
$mysqli = new mysqli('localhost', 'aire', 'aire', 'prueba_aire');
if ($mysqli->connect_error) {
    die('Error de Conexión (' . $mysqli->connect_errno . ') '
        . $mysqli->connect_error);
}
$result = array('result' => "KO", 'time' => 0);
$datos = "ejercicio_datos";
$llamadas = "ejercicio_llamadas";

$mysqli->query('truncate table llamadas_a_tratar');
$data = filetoarray($llamadas, ";")[1];
$queryInsert = "insert into llamadas_a_tratar values";
$values = array();
foreach ($data as $llamada) {
    $origen = $llamada[0];
    $destino = $llamada[1];
    $descripcion = $llamada[2];
    $cliente = $llamada[3];
    $fecha_array = explode("/", $llamada[4]);
    $fecha = $fecha_array[2] . "-" . $fecha_array[1] . "-" . $fecha_array[0];
    $hora = $llamada[5];
    $segundos = $llamada[6];
    $precio = str_replace(',', '.', $llamada[7]);
    $query = "(default,'{$origen}','{$destino}','{$descripcion}',{$cliente}," .
        "'{$fecha}','{$hora}',{$segundos},{$precio})";
    array_push($values, $query);
}
$fullQuery = $queryInsert . implode(", ", $values);
if (!$mysqli->query($fullQuery) === true) {
    $time = microtime(true) - $time_start;
    $result['time'] = $time;
    $result['msg'] = 'Error en tabla llamadas.';
    echo (json_encode($result));
    die();
}

$mysqli->query('truncate table datos_a_tratar');
$data = filetoarray($datos, ";")[1];
$queryInsert = "insert into datos_a_tratar values";
$values = array();
foreach ($data as $llamada) {
    $origen = $llamada[0];
    $descripcion = $llamada[1];
    $cliente = $llamada[2];
    $fecha_array = explode("/", $llamada[3]);
    $fecha = $fecha_array[2] . "-" . $fecha_array[1] . "-" . $fecha_array[0];
    $hora = $llamada[4];
    $segundos = $llamada[5];
    $precio = str_replace(',', '.', $llamada[6]);
    $query = "(default,'{$origen}','{$descripcion}',{$cliente}," .
        "'{$fecha}','{$hora}',{$segundos},{$precio})";
    array_push($values, $query);
}
$fullQuery = $queryInsert . implode(", ", $values);
if (!$mysqli->query($fullQuery) === true) {
    $time = microtime(true) - $time_start;
    $result['time'] = $time;
    $result['msg'] = 'Error en tabla datos.';
    echo (json_encode($result));
    die();
}

$mysqli->close();
$time = microtime(true) - $time_start;
$result['time'] = $time;
$result['result'] = 'OK';
echo (json_encode($result));
