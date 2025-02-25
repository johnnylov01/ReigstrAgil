<?php

include "conexion.php";

// CORS
header('Access-Control-Allow-Origin: http://localhost:5173');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $json = file_get_contents('php://input');
    
    // Eliminar las comillas dobles porque al momento de recibir aquí el correo lo recibe como "correo@gmail.com"
    $email = trim($json, '"');

    //Validamos primeramente que no tenga juntas agendadas
    $sql = "SELECT id_Anfitrion FROM anfitrion WHERE correo = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id);

    if ($stmt->fetch()) { // se encontró el correo y se guardó
        $id = intval($id); // convertir $id a un entero
        $stmt->close();

        // Comprobar si hay juntas agendadas con ese id_Anfitrion
        $sql2 = "SELECT * FROM junta WHERE id_Anfitrion = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $result = $stmt2->get_result();

        if ($result->num_rows > 0) {
            // Si hay juntas agendadas
            echo json_encode(array("error" => "tiene juntas agendadas este empleado"));
        } else {
            // Eliminar el registro de la tabla anfitrion con el $id_anfitrion
            $sql_delete_anfitrion = "DELETE FROM anfitrion WHERE id_Anfitrion = ?";
            $stmt_delete_anfitrion = $conn->prepare($sql_delete_anfitrion);
            $stmt_delete_anfitrion->bind_param("i", $id_anfitrion);
            $stmt_delete_anfitrion->execute();
            $stmt_delete_anfitrion->close();

            // Eliminar el registro de la tabla usuario con el correo
            $sql_delete_usuario = "DELETE FROM usuario WHERE correo = ?";
            $stmt_delete_usuario = $conn->prepare($sql_delete_usuario);
            $stmt_delete_usuario->bind_param("s", $email);
            $stmt_delete_usuario->execute();
            $stmt_delete_usuario->close();
            
            echo json_encode(array("message"=>"registro eliminado"));
            
        }

    } else {
        echo json_encode(array("error" => "no se encontró registro"));
    }
} else {
    echo json_encode(array("error" => "No se realizó la petición de manera correcta"));
}

$conn->close();

?>