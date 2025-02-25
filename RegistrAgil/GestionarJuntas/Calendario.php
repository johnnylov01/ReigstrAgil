<?php
// Conexion a la base 
include("conexion.php");

session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json");

try {
    if(session_status() == PHP_SESSION_ACTIVE) {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Leer el contenido del cuerpo de la solicitud
            $json = file_get_contents('php://input');
            // Decodificar el JSON a un array asociativo
            $data = json_decode($json, true);

            // Verificar si se pudo decodificar el JSON correctamente
            if ($data === null) {
                throw new Exception("Error al decodificar el JSON");
            }
            $fecha = $data["fecha"];

            //Consulta a los datos de la junta
            $sql1 = "SELECT 
                        j.concepto AS asunto,
                        j.sala AS sala,
                        j.fecha AS fecha,
                        DATE_FORMAT(j.horaFin, '%H:%i') AS horaFin,
                        DATE_FORMAT(j.horaInicio, '%H:%i') AS horaInicio,
                        j.ubicacion AS direccion,
                        j.descripcion AS descripcion,
                        CONCAT(u.nombre, ' ', u.apellido_paterno, ' ', u.apellido_materno) AS anfitrion,
                        GROUP_CONCAT(i.correo SEPARATOR ', ') AS invitados
                    FROM 
                        junta j
                    INNER JOIN 
                        anfitrion a ON j.id_Anfitrion = a.id_Anfitrion
                    INNER JOIN 
                        usuario u ON a.correo = u.correo
                    LEFT JOIN 
                        reuniones r ON j.id_Anfitrion = r.id_Anfitrion AND j.fecha = r.fecha AND j.sala = r.sala
                    LEFT JOIN 
                        invitado i ON r.id_Invitado = i.id_Invitado
                    WHERE DATE_FORMAT(j.fecha, '%Y-%m') = DATE_FORMAT(?, '%Y-%m')
                    GROUP BY j.concepto, j.sala, j.fecha, j.horaFin, j.horaInicio, j.ubicacion, j.descripcion, u.nombre, u.apellido_paterno, u.apellido_materno";

            // Preparar la consulta
            $stmt = $conn->prepare($sql1);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conn->error);
            }

            $stmt->bind_param("s", $fecha);

            // Ejecutar la consulta
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result) {
                // Crear un array para almacenar los resultados
                $dataArray = array();

                // Recorrer los resultados y agregar cada fila al array
                while ($row = $result->fetch_assoc()) {
                    $row['invitados'] = array_filter(explode(', ', $row['invitados']));
                    $dataArray[] = $row;
                }

                echo json_encode($dataArray);
            } else {
                throw new Exception("Error al visualizar juntas en el calendario: " . $stmt->error);
            }
        } else {
            throw new Exception("No se ha enviado una solicitud POST");
        }
    } else {
        throw new Exception("No se ha iniciado sesión");
    }
} catch (Exception $e) {
    echo json_encode(array("success" => false, "message" => $e->getMessage()));
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
