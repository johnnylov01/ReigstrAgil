<?php
include("conexion.php");
session_start();

// CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(array("error" => "Invalid JSON: " . json_last_error_msg()));
        exit;
    }
    
    $correoAnfitrion = $data["correoAnfitrion"];
    $asunto = $data["asunto"];
    $sala = $data["sala"];
    $fecha = $data["fecha"];
    $horaI = $data["horai"];
    $horaF = $data["horaf"];
    $direccion = $data["direccion"];
    $descripcion = $data["descripcion"];
    $invitados = $data["invitados"];

    // Obtener el id_Anfitrion a partir del correo del anfitrión
    $sql_anfitrion_id = "SELECT id_Anfitrion FROM anfitrion WHERE correo = ?";
    $stmt_get_anfitrion_id = $conn->prepare($sql_anfitrion_id);
    $stmt_get_anfitrion_id->bind_param("s", $correoAnfitrion);
    $stmt_get_anfitrion_id->execute();
    $stmt_get_anfitrion_id->bind_result($id_Anfitrion);
    $stmt_get_anfitrion_id->fetch();
    $stmt_get_anfitrion_id->close();

    if (!isset($id_Anfitrion)) {
        echo json_encode(array("error" => "Anfitrión no encontrado"));
        exit;
    }
    //consultar si la junta ya existe
    $existencia="SELECT * from reuniones where fecha='".$fecha."' and horaInicio='".$horaI."' and id_Anfitrion='".$id_Anfitrion."' and sala='".$sala."'";
    $resultadoexist=$conn->query($existencia);
    //si ya existe, se manda un mensaje de error
    if($resultadoexist->num_rows>0)
    {
        echo json_encode(array("error" => "Horario ocupado"));
    }else{
        // Insertar la junta en la base de datos
        $sql = "INSERT INTO junta (descripcion, horaFin, ubicacion, sala, fecha, concepto, horaInicio, id_Anfitrion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_junta = $conn->prepare($sql);
        $stmt_junta->bind_param("sssssssi", $descripcion, $horaF, $direccion, $sala, $fecha, $asunto, $horaI, $id_Anfitrion);

        // Ejecutar la consulta SQL
        if ($stmt_junta->execute()) {
            // Obtener el ID de la junta recién insertada
            $junta_id = $stmt_junta->insert_id;

            // Preparar la consulta para insertar invitados en la tabla usuario
            $stmt_usuario = $conn->prepare("INSERT INTO usuario (correo, permisos) VALUES (?, 2) ON DUPLICATE KEY UPDATE correo=correo");

            // Preparar la consulta para insertar invitados en la tabla invitado
            $stmt_invitado = $conn->prepare("INSERT INTO invitado (correo) VALUES (?) ON DUPLICATE KEY UPDATE correo=correo");

            // Preparar la consulta para asociar invitados con reuniones
            $stmt_reunion = $conn->prepare("INSERT INTO reuniones (id_Invitado, sala, fecha, horaInicio, id_Anfitrion, numAcompañantes) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_reunion->bind_param("isssss", $id_Invitado, $sala, $fecha, $horaI, $id_Anfitrion, $numAcompanantes);
            // Recorrer el array de invitados y ejecutar las inserciones
            foreach ($invitados as $invitado) {
                //buscar los correos de usuarios que ya existen
                
                $correo = $invitado["correo"];
                $numAcompanantes = $invitado["acompañantes"];
                $verificar=$conn->query("SELECT * from usuario where correo='".$correo."'");
                if($verificar->num_rows>0){
                    if($conn->query("SELECT * from invitado where correo='".$correo."'")->num_rows==0)
                    {
                        $stmt_invitado->bind_param("s", $correo);
                        $stmt_invitado->execute();
                        $id_Invitado = $stmt_invitado->insert_id;
                    }else{
                        $id_Invitado=$conn->query("SELECT id_Invitado from invitado where correo='".$correo."'")->fetch_assoc()["id_Invitado"];
                    }
                }else{
                    $stmt_usuario->bind_param("s", $correo);
                    $stmt_usuario->execute();
                    $stmt_invitado->bind_param("s", $correo);
                    $stmt_invitado->execute();
                    $id_Invitado = $stmt_invitado->insert_id;
                }
                // Obtener el ID del invitado recién insertado
                $stmt_reunion->bind_param("issssi", $id_Invitado, $sala, $fecha, $horaI, $id_Anfitrion, $numAcompanantes);
                $stmt_reunion->execute();
            }

            // Cerrar los statements
            $stmt_usuario->close();
            $stmt_invitado->close();
            $stmt_reunion->close();
            $stmt_junta->close();
            // Enviar una respuesta JSON con éxito
            echo json_encode(array("message" => "Junta e invitados creados correctamente"));
        } else {
            // Enviar una respuesta JSON con error si la consulta falla
            echo json_encode(array("error" => "Error al crear la junta: " . $stmt_junta->error));
        }
    }
    
} else {
    // Enviar una respuesta JSON con error si no se ha enviado una solicitud POST
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}

// Cerrar la conexión a la base de datos
$conn->close();
?>