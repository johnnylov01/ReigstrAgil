<?php
header('Access-Control-Allow-Origin: *');

include("conexion.php");

session_start();

header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json");

if (session_status() == PHP_SESSION_ACTIVE) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if ($data === null) {
            // http_response_code(400);
            echo json_encode(array("error" => "Error al decodificar el JSON"));
            exit();
        }

        $fecha = $data["Fecha"];
        $sql1 = "SELECT
            GROUP_CONCAT(DISTINCT entradas.horaEntrada ORDER BY entradas.horaEntrada ASC SEPARATOR ', ') AS HoraEntrada,
            GROUP_CONCAT(DISTINCT salidas.horaSalida ORDER BY salidas.horaSalida ASC SEPARATOR ', ') AS HoraSalida,
            CASE
                WHEN codigoqr.id_Invitado IS NOT NULL THEN 'Invitado'
                ELSE 'Acompañante'
            END AS TipoPersona,
            CONCAT(usuario.nombre, ' ', usuario.apellido_paterno, ' ', usuario.apellido_materno) AS Nombre,
            usuario.fotografia AS Fotografia,
            usuario.telefono AS Telefono,
            usuario.correo AS Email,
            GROUP_CONCAT(DISTINCT dispositivo.NoSerie SEPARATOR ', ') AS NoSerieDispositivo,
            GROUP_CONCAT(DISTINCT dispositivo.modelo SEPARATOR ', ') AS ModeloDispositivo,
            auto.modelo AS ModeloAuto,
            auto.placa AS PlacaAuto,
            auto.color AS ColorAuto,
            junta.sala AS Sala, 
            CONCAT(anfitrion_usuario.nombre, ' ', anfitrion_usuario.apellido_paterno, ' ', anfitrion_usuario.apellido_materno) AS Encargado,
            junta.concepto AS AsuntoJunta,
            junta.fecha AS Fecha
        FROM
            codigoqr
        LEFT JOIN (SELECT e.id_Codigo,
            GROUP_CONCAT(e.horaEntrada ORDER BY e.horaEntrada ASC SEPARATOR ', ') AS horaEntrada FROM entradas e
        GROUP BY e.id_Codigo ) entradas ON codigoqr.id_Codigo = entradas.id_Codigo
        LEFT JOIN (SELECT s.id_Codigo,
            GROUP_CONCAT(s.horaSalida ORDER BY s.horaSalida ASC SEPARATOR ', ') AS horaSalida FROM salidas s
        GROUP BY s.id_Codigo ) salidas ON codigoqr.id_Codigo = salidas.id_Codigo
        LEFT JOIN
            invitado ON codigoqr.id_Invitado = invitado.id_Invitado
        LEFT JOIN
            acompañante ON codigoqr.id_Acompañante = acompañante.id_Acompañante
        LEFT JOIN 
            auto ON acompañante.id_Acompañante = auto.id_Acompañante OR invitado.id_Invitado = auto.id_Invitado
        LEFT JOIN
            dispositivo ON acompañante.id_Acompañante = dispositivo.id_Acompañante OR invitado.id_Invitado = dispositivo.id_Invitado
        LEFT JOIN
            usuario ON (invitado.correo = usuario.correo OR acompañante.correo = usuario.correo)
        LEFT JOIN
            junta ON codigoqr.horaInicio = junta.horaInicio AND codigoqr.fecha = junta.fecha AND codigoqr.sala = junta.sala AND codigoqr.id_Anfitrion = junta.id_Anfitrion
        LEFT JOIN
            anfitrion ON junta.id_Anfitrion = anfitrion.id_Anfitrion
        LEFT JOIN
            usuario AS anfitrion_usuario ON anfitrion.correo = anfitrion_usuario.correo
        WHERE codigoqr.fecha = ?
        GROUP BY
            usuario.correo,
            TipoPersona,
            Nombre,
            Fotografia,
            Encargado,
            AsuntoJunta,
            Sala
        ORDER BY
            usuario.correo, MIN(entradas.horaEntrada);";

        $stmt = $conn->prepare($sql1);
        $stmt->bind_param("s", $fecha);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            $dataArray = array();
            while ($row = $result->fetch_assoc()) {
                if (!empty($row['Fotografia'])) {
                    $row['Fotografia'] = base64_encode($row['Fotografia']);
                }
                $dataArray[] = $row;
            }
            // http_response_code(200);
            echo json_encode(array("success" => true, "dataArray" => $dataArray));
        } else {
            // http_response_code(500);
            echo json_encode(array("success" => false, "message" => "Error al ejecutar la consulta: " . $conn->error));
        }

        $stmt->close();
    } else {
        // http_response_code(405);
        echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
    }

    $conn->close();
} else {
    http_response_code(401);
    echo json_encode(array("error" => "No se ha iniciado sesión"));
}
?>
