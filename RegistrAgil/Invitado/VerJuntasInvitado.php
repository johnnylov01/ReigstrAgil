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
        // Obtener la fecha de la solicitud
        $correo = $data["correo"];
        // Iniciar una transacción
        $conn->begin_transaction();

        try {
            // Preparar la consulta SQL para visualizar las juntas activas junto a los invitados
            $sql = "SELECT 
            CONCAT (u.nombre, ' ', u.apellido_paterno , ' ', u.apellido_materno) AS Responsable,
            u.correo AS CorreoAnfitrion,
            j.concepto AS Asunto,
            j.sala AS Sala,
            j.fecha AS Fecha,
            CONCAT( j.horaInicio, '-', j.horaFin ) AS Horario,
            qr.id_Codigo as CodigoQR,
            CONCAT(u_inv.nombre, ' ', u_inv.apellido_paterno, ' ', u_inv.apellido_materno) AS Nombre
            FROM 
            junta j
            INNER JOIN 
            anfitrion a ON j.id_Anfitrion = a.id_Anfitrion
            LEFT JOIN 
            reuniones r ON j.id_Anfitrion = r.id_Anfitrion AND j.fecha = r.fecha AND j.sala = r.sala AND j.horaInicio = r.horaInicio
            LEFT JOIN 
            invitado i ON i.id_Invitado = r.id_Invitado
            LEFT JOIN codigoqr qr ON
            (qr.id_Invitado IS NOT NULL AND qr.id_Invitado = i.id_Invitado)
            OR
            (qr.id_Acompañante IS NOT NULL AND qr.id_Acompañante = i.id_Invitado)
            LEFT JOIN 
            usuario u_inv ON i.correo = u_inv.correo
            LEFT JOIN 
            usuario u ON a.correo = u.correo
            WHERE u_inv.correo= ?
            GROUP BY 
            j.sala,j.horaInicio, j.id_Anfitrion,j.fecha, a.correo;";
            // --Checamos en la tabla 'acompañante' si el qr.id_Acompañante pertenece a un id_Acompañante y lo comparamos con el id de esa tabla
            // -- (qr.id_Acompañante IS NOT NULL AND (LEFT JOIN acompañante ac ON qr.id_Acompañante = ac.id_Acompañante AND ac.id_Invitado = i.id_Invitado))
            // Preparar la consulta
            $stmt = $conn->prepare($sql);
            // Vincular los parámetros
            $stmt->bind_param("s", $correo);
            // Ejecutar la consulta
            $stmt->execute();
            $result = $stmt->get_result();
            // Verificar si la consulta se ejecutó correctamente
            if ($result) {
                // Crear un array para almacenar los resultados
                
                $juntas = [];   
                while ($row = $result->fetch_assoc()) {
                    $juntas[] = $row;
                }
                //file_put_contents('C:\xampp\htdocs\Proyecto\RegistrAgil\RegistrAgil\Juntas\meetings-app\public\hola.json', json_encode($juntas, JSON_PRETTY_PRINT));
                echo json_encode($juntas);

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
    //$conn->close();

}

?>
