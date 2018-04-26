<?php
function descargarFicheros($ip, $user, $password, $ficheros)
{
    $result = true;
    $conn = ftp_connect($ip) or die("No se pudo conectar a " . $ip);
    if (@ftp_login($conn, $user, $password)) {
        ftp_pasv($conn, true);
        foreach ($ficheros as $file) {
            if (!ftp_get($conn, $file, $file, FTP_BINARY)) {
                echo "error";
                $result = false;
                break;
            }
        }
        ftp_close($conn);
    } else {
        $result = false;
    }
    return $result;
}
function filetoarray($nombre, $separador)
{
    $fila = 0;
    $titulo = [];
    $data = [];
    if (($gestor = fopen($nombre . ".csv", "r")) !== false) {
        while (($datos = fgetcsv($gestor, 0, $separador)) !== false) {
            $fila++;
            $length = count($datos);
            if ($fila <= 1) {
                for ($i = 0; $i < $length; $i++) {
                    $titulo[strtolower($datos[$i])] = $i;
                }
                continue;
            }
            for ($i = 0; $i < $length; $i++) {
                $data[$fila - 2][$i] = $datos[$i];
            }
        }
        fclose($gestor);
    }
    return [$titulo, $data];
}
