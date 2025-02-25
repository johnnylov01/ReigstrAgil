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

    $descripcion = $data["descripcion"];
    $asunto = $data["asunto"];
    $horaInicio = $data["hora_inicio"];
    $horaInicioActual = $data["hora_inicio_Actual"];
    $horaFin = $data["hora_fin"];
    $fecha = $data["fecha"];
    $fechaActual = $data["fecha_Actual"];
    $sala = $data["sala"];
    $salaActual = $data["sala_Actual"];
    $correo= $data["correo"]; 

    // Preparar la consulta SQL para insertar los datos en la tabla de empleados
    $sql = "UPDATE junta
            SET descripcion = '$descripcion',
                horaFin = '$horaFin',
                sala = '$sala',
                fecha = '$fecha',
                concepto = '$asunto',
                horaInicio = '$horaInicio'
            WHERE horaInicio = '$horaInicioActual' AND
                  fecha = '$fechaActual' AND
                  sala = '$salaActual' AND
                  id_Anfitrion = (SELECT id_Anfitrion FROM anfitrion WHERE correo = '$correo');";

    // Ejecutar la consulta SQL
        
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("success" => true,"message" => "La junta se ha actualizado correctamente."));
    } else {
        echo json_encode(array("success" => false,"message" => "Error al actualizar la junta: " . $conn->error));
    }
} else {
    // Enviar una respuesta JSON con error si no se ha enviado una solicitud POST
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}

// Cerrar la conexión a la base de datos
$conn->close();



?>
