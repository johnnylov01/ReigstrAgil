<?php
// Conexion a la base 
include("conexion.php");

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

    $fecha = $data["Fecha"];

    //Consulta a los datos de las entradas y salidas
    $sql1 = "SELECT
    GROUP_CONCAT(DISTINCT entradas.horaEntrada ORDER BY entradas.horaEntrada ASC SEPARATOR ', ') AS HoraEntrada,
    GROUP_CONCAT(DISTINCT salidas.horaSalida ORDER BY salidas.horaSalida ASC SEPARATOR ', ') AS HoraSalida,
    CASE
        WHEN codigoqr.id_Invitado IS NOT NULL THEN 'Invitado'
        ELSE 'Acompañante'
    END AS TipoPersona,
    usuario.nombre AS Nombre,
    usuario.apellido_paterno AS ApellidoPaterno,
    usuario.apellido_materno AS ApellidoMaterno,
    usuario.fotografia AS Fotografia,
    anfitrion_usuario.nombre AS NombreAnfitrion,
    anfitrion_usuario.apellido_paterno AS Apellido_Pat_Anfitrion,
    anfitrion_usuario.apellido_materno AS Apellido_Mat_Anfitrion,
    junta.concepto AS ConceptoJunta,
    junta.sala AS NombreSala,
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
    ApellidoPaterno,
    ApellidoMaterno,
    Fotografia,
    NombreAnfitrion,
    Apellido_Pat_Anfitrion,
    Apellido_Mat_Anfitrion,
    ConceptoJunta,
    NombreSala
    ORDER BY
    usuario.correo, MIN(entradas.horaEntrada);";

    // Preparar la consulta
    $stmt = $conn->prepare($sql1);
    $stmt->bind_param("s", $fecha);

    // Ejecutar la consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Ejecutar la consulta SQL      
     $result = $conn->query($sql1);
    // Verificar si la consulta se ejecutó correctamente
    if ($result) {
        // Crear un array para almacenar los resultados
        $dataArray = array();

        // Recorrer los resultados y agregar cada fila al array
        while ($row = $result->fetch_assoc()) {
           // Convertir la fotografía a base64
            $row['Fotografia'] = base64_encode($row['Fotografia']);
            $dataArray[] = $row;
        }

        echo json_encode($dataArray);
        echo json_encode(array("success" => true,"message" => "La consulta a las entradas y salidas se ha realizado correctamente."));
    } else {
        echo json_encode(array("success" => false,"message" => "Error al actualizar visualizar entradas y salidas: " . $conn->error));
    }
} else {
    // Enviar una respuesta JSON con error si no se ha enviado una solicitud POST
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}

// Cerrar la conexión a la base de datos
$conn->close();

?>