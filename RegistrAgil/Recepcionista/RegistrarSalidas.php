<?php
// Conexion a la base 
include("conectar.php");

// CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json; charset=utf-8");

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

    $horaSalida = $data["hora"];
    $id_Codigo = $data["id_Codigo"];

    $conn = conectarDB();
    // Iniciar una transacción
    $conn->begin_transaction();
    try {
        // Insertar la hora de entrada en la base de datos
        $sql1 = "INSERT INTO salidas(horaSalida, id_Codigo) VALUES (?,?)";

        // Preparar la consulta
        $stmt = $conn->prepare($sql1);
        if ($stmt === false) {
            throw new Exception("Error al preparar la consulta de inserción: " . $conn->error);
        }
        $stmt->bind_param("si", $horaSalida, $id_Codigo);

        // Ejecutar la consulta
        if (!$stmt->execute()) {
            throw new Exception("Error al ejecutar la consulta de inserción: " . $stmt->error);
        }
        $stmt->close();

       
        // Confirmar la transacción 
        $conn->commit();     
        echo json_encode(array("success" => true, "message" => "El registro de la salida se ha realizado correctamente."));
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        echo json_encode(array("success" => false, "message" => $e->getMessage()));
    }
} else {
    // Enviar una respuesta JSON con error si no se ha enviado una solicitud POST
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
