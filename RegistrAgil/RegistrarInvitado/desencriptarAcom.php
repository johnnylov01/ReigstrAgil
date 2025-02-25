<?php

// CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json; charset=utf-8");


function descifrar($mensaje, $llave){
    list($datos_encriptados, $inivec) = explode('::', base64_decode($mensaje), 2);
    return openssl_decrypt($datos_encriptados, 'AES-256-CBC', $llave, 0, $inivec);
}

$entrada = file_get_contents('php://input');
$request = json_decode($entrada);

$encryptedData = $request->encryptedData; // Obtiene los datos cifrados desde la solicitud POST


$respuesta = descifrar($encryptedData, 'softwareLegendsEsGOD');
echo $respuesta;

// Decodificar el JSON a un objeto PHP
/*$respuesta = json_decode($respuesta);

echo json_encode(array("sala" => $respuesta->sala,
                        "fecha" => $respuesta->fecha,
                        "horaInicio" => $respuesta->horaInicio,
                        "id_Anfitrion" => $respuesta->id_Anfitrion,
                        "correo" => $respuesta->correo,
                        "id_Invitado" => $respuesta -> id_Invitado));
*/
?>
