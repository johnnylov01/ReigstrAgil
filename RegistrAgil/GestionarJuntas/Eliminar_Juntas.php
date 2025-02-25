<?php

include("conexion.php");

// CORS
header('Access-Control-Allow-Origin: *');

header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");

header("Content-Type: application/json; charset=utf-8");

// Verificar si se ha enviado una solicitud POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Leer el contenido del cuerpo de la solicitud
    $json = file_get_contents('php://input');
    // Decodificar el JSON a un array asociativo
    $data = json_decode($json, true);

    // Verificar si se pudo decodificar el JSON correctamente
    if ($data === null) {
        // Enviar una respuesta JSON con error si hay un problema con el JSON
        echo json_encode(array("error" => "Error al decodificar el JSON"));
        exit();
    }

    // Recibir y validar cada campo del formulario
    //Dado que aqui no estamos jalando los datos del formulario directamente


    $horaInicio = $data["hora_inicio"];
    $fecha = $data["fecha"];
    $sala = $data["sala"];
    $correo= $data["correo"]; 

    // Preparar la consulta SQL para insertar los datos en la tabla de empleados
    $sql = "DELETE FROM junta
    WHERE horaInicio = '$horaInicio'
    AND sala = '$sala'
    AND id_Anfitrion = (select id_Anfitrion from anfitrion where correo= '$correo')
    AND fecha = '$fecha'";

    // Ejecutar la consulta SQL
        
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("success" => true,"message" => "La junta se ha eliminado correctamente."));
    } else {
        echo json_encode(array("success" => false,"message" => "Error al eliminar la junta: " . $conn->error));
    }
} else {
    // Enviar una respuesta JSON con error si no se ha enviado una solicitud POST
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}

// Cerrar la conexión a la base de datos
$conn->close();



?>
