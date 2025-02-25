<?php

include("conexion.php");
session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json");

if(session_status()== PHP_SESSION_ACTIVE){

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
        $conn->set_charset('utf8');

        // Obtener el correo de la sesion
        $correo = $data["correo"];
        $correoAnfitrion = $data["correoAnfitrion"];
        $fecha = $data["fecha"];
        $horaInicio = $data["horaInicio"];
        $sala = $data["sala"];
        // Iniciar una transacción
        $conn->begin_transaction();

        try {
            // Preparar la consulta SQL para visualizar el qr y las juntas activas de un invitado
            $sql = "SELECT 
            CONCAT(u_inv.nombre, ' ', u_inv.apellido_paterno, ' ', u_inv.apellido_materno) AS Nombre,
            j.fecha AS Fecha,
            j.horaInicio AS HoraInicio,
            j.horaFin AS HoraFin,
            j.concepto AS asunto,
            j.sala AS Sala,
            CONCAT (u.nombre, ' ', u.apellido_paterno , ' ', u.apellido_materno) AS Responsable,
            CONCAT ( j.fecha ,', ', qr.caducidad)as FechaCaducida,
            qr.id_Codigo as CodigoQR
            FROM 
            junta j
            INNER JOIN 
            anfitrion a ON j.id_Anfitrion = a.id_Anfitrion
            LEFT JOIN 
            reuniones r ON j.id_Anfitrion = r.id_Anfitrion AND j.fecha = r.fecha AND j.sala = r.sala AND j.horaInicio = r.horaInicio
            LEFT JOIN 
            invitado i ON i.id_Invitado = r.id_Invitado
            LEFT JOIN codigoqr qr ON
            (qr.id_Invitado IS NOT NULL AND qr.id_Invitado = i.id_Invitado AND qr.fecha = j.fecha AND qr.sala = j.sala)
            OR
            (qr.id_Acompañante IS NOT NULL AND qr.id_Acompañante = i.id_Invitado AND qr.fecha = j.fecha AND qr.sala = j.sala)
            LEFT JOIN 
            usuario u_inv ON i.correo = u_inv.correo
            LEFT JOIN 
            usuario u ON a.correo = u.correo
            WHERE u_inv.correo= ? and j.sala= ? and j.fecha= ? and j.horaInicio= ? and a.correo= ?
            GROUP BY 
            j.sala,j.horaInicio, j.id_Anfitrion,j.fecha, a.correo;";

            // Preparar la consulta
            $stmt = $conn->prepare($sql);
            // Vincular los parámetros
            $stmt->bind_param("sssss", $correo, $sala, $fecha, $horaInicio, $correoAnfitrion);
            // Ejecutar la consulta
            $stmt->execute();
           

            $result = $stmt->get_result();
            // Verificar si la consulta se ejecutó correctamente
            if ($result) {
                // Crear un array para almacenar los resultados
                
                $juntas = [];   
                while ($row = $result->fetch_assoc()) {
                    // $row["CodigoQR"] = base64_encode($row["CodigoQR"]);
                    $juntas[] = $row;
                }
                // $foto = base64_encode($datos['fotografia']);
                // $juntas[0]["CodigoQR"] = base64_encode($juntas[0]["CodigoQR"]);
                echo json_encode($juntas);
                //echo json_encode(array("success" => true,"message" => "La consulta a las juntas se ha realizado correctamente."));

            } else {
                echo json_encode(array("success" => false,"message" => "Error al visualizar Juntas del invitado: " . $conn->error));
            }

            // Confirmar la transacción
            $conn->commit();

        } 
        catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conn->rollback();
            echo json_encode(array("success" => false, "message" => $e->getMessage())); 
        }
        // Cerrar las declaraciones
        $stmt->close();

    } else {
        // Enviar una respuesta JSON con error si no se ha enviado una solicitud POST
        echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
    }

    // Cerrar la conexión a la base de datos
    $conn->close();


}
else{
    echo json_encode(array("error" => "No se ha iniciado una sesión"));
}
?>
