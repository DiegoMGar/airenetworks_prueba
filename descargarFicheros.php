<?php
$time_start = microtime(true);
require "helpers.php";
require "datosacceso.php";
$datos = "ejercicio_datos.csv";
$llamadas = "ejercicio_llamadas.csv";
$ficheros = [$datos, $llamadas];

$result = array('result' => "", 'time' => 0);
if (descargarFicheros($ip, $user, $password, $ficheros)) {
    $result['result'] = "OK";
} else {
    $result['result'] = "KO";
    $result['msg'] = 'Error descargando ficheros.';
}
$time = microtime(true) - $time_start;
$result['time'] = $time;

echo (json_encode($result));
