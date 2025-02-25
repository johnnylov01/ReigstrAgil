<?php
$conexion = mysqli_connect("localhost", "root", "", "baseregistragil");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los nuevos valores enviados por el cliente
    $arrayJson = file_get_contents('php://input');
    $dataArray = json_decode($arrayJson, true);
    $correo = $dataArray["correo"];
    $nuevaEmpresa = $dataArray["nueva_empresa"];
    $nuevoTelefono = $dataArray["nuevo_telefono"];
    $nuevoEquipoSerie = $dataArray["nuevo_equipo_serie"];
    $nuevoEquipoModelo = $dataArray["nuevo_equipo_modelo"];
    $nuevoCochePlaca = $dataArray["nuevo_coche_placa"];
    $nuevoCocheColor = $dataArray["nuevo_coche_color"];
    $nuevoCocheModelo = $dataArray["nuevo_coche_modelo"];
    $nuevafotografia = $dataArray["fotografia"];

    //Pasamos a tipo blob la fotografia para poder guardarla en la base de datos
    $nuevafotografia = base64_decode($nuevafotografia);
    

    //Decodificamos la foto a base64
    // $nuevafotografia = base64_encode($nuevafotografia);

    // $correo = $_POST['correo'];
    // $nuevaEmpresa = $_POST["nueva_empresa"];
    // $nuevoTelefono = $_POST["nuevo_telefono"];
    // $nuevoEquipoSerie = $_POST["nuevo_equipo_serie"];
    // $nuevoEquipoModelo = $_POST["nuevo_equipo_modelo"];
    // $nuevoCochePlaca = $_POST["nuevo_coche_placa"];
    // $nuevoCocheColor = $_POST["nuevo_coche_color"];
    // $nuevoCocheModelo = $_POST["nuevo_coche_modelo"];
    // $nuevafotografia = $_POST["fotografia"];

    //Obtener el ID de invitado
    $select_ip_invitado = "SELECT id_Invitado FROM invitado WHERE correo = '$correo'";
    $id_invitadoi = mysqli_query($conexion, $select_ip_invitado);
    $fila_id_invitado = mysqli_fetch_assoc($id_invitadoi);
    $id_invi = $fila_id_invitado['id_Invitado'];

    $stmt = $conexion->prepare("UPDATE usuario SET empresa = ?, telefono = ?, fotografia = ? WHERE correo = ?");
    $stmt->bind_param("ssbs", $nuevaEmpresa, $nuevoTelefono, $nuevafotografia, $correo);
    $stmt->send_long_data(2, $nuevafotografia);
    if (!$stmt->execute()) {
        echo json_encode(array("error" => "Error al actualizar empresa, telefono y/o foto: " . $conexion->error));
        exit;
    }
    $stmt->close();

    // Actualizar los valores en la base de datos
    // $update_usuario_query = "UPDATE usuario SET empresa = '$nuevaEmpresa', telefono = '$nuevoTelefono', fotografia = $nuevafotografia WHERE correo = '$correo'";
    // if (!mysqli_query($conexion, $update_usuario_query)) {
    //     echo json_encode(array("error" => "Error al actualizar empresa, telefono y/o foto: " . $conexion->error));
    //     exit;
    // }

    // Actualizar equipo
    //select para ver si existen equipos
    $select_checar_equipo = "SELECT fecha, horaInicio FROM dispositivo WHERE id_Invitado = '$id_invi' ORDER BY ABS(TIMESTAMPDIFF(SECOND, TIMESTAMP(fecha, horaInicio), NOW())) LIMIT 1";
    $conteo = mysqli_query($conexion,$select_checar_equipo);
    if (($conteo->num_rows > 0) ) {
        $row = mysqli_fetch_assoc($conteo);    
        // Extraer los valores de fecha y horaInicio
        $fechaE = $row['fecha'];
        $horaInicioE = $row['horaInicio'];
        //Actualiza si ya existen datos en la base de datos
        $update_equipo_query = "UPDATE dispositivo SET NoSerie = '$nuevoEquipoSerie', modelo = '$nuevoEquipoModelo' WHERE id_Invitado = '$id_invi' and fecha = '$fechaE' and horaInicio = '$horaInicioE'";
        if (!mysqli_query($conexion, $update_equipo_query)) {
            echo json_encode(array("error" => "Error al actualizar el equipo: " . $conexion->error));
            exit;
        }
    } else {
        //Crea el equipo si es que no habia datos en la base de datos y no las variables no tienen el valor "No aplica"
        $select_horario_proximoE = "SELECT fecha, horaInicio FROM reuniones WHERE id_Invitado = '$id_invi' ORDER BY ABS(TIMESTAMPDIFF(SECOND, TIMESTAMP(fecha, horaInicio), NOW())) LIMIT 1";
        $row = mysqli_fetch_assoc(mysqli_query($conexion, $select_horario_proximoE));    
        // Extraer los valores de fecha y horaInicio
        $fechaE = $row['fecha'];
        $horaInicioE = $row['horaInicio'];
        $insert_equipo_query = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado, fecha, horaInicio) VALUES ('$nuevoEquipoSerie','$nuevoEquipoModelo','$id_invi','$fechaE','$horaInicioE')";
        if (!mysqli_query($conexion, $insert_equipo_query)) {
            echo json_encode(array("error" => "Error al Insertar el equipo: " . $conexion->error));
            exit;
        }
    }

    // Actualizar vehiculo
    //select para ver si existen equipos
    $select_checar_auto = "SELECT fecha, horaInicio FROM auto WHERE id_Invitado = '$id_invi' ORDER BY ABS(TIMESTAMPDIFF(SECOND, TIMESTAMP(fecha, horaInicio), NOW())) LIMIT 1";
    $conteoA = mysqli_query($conexion,$select_checar_auto);
    if ($conteoA->num_rows > 0 ) {
        $row = mysqli_fetch_assoc($conteoA);    
        // Extraer los valores de fecha y horaInicio
        $fechaA = $row['fecha'];
        $horaInicioA = $row['horaInicio'];
        //Actualiza si ya existen datos en la base de datos
        $update_auto_query = "UPDATE auto SET placa = '$nuevoCochePlaca', color = '$nuevoCocheColor', modelo = '$nuevoCocheModelo' WHERE id_Invitado = '$id_invi' and fecha = '$fechaA' and horaInicio = '$horaInicioA'";
        if (!mysqli_query($conexion, $update_auto_query)) {
            echo json_encode(array("error" => "Error al actualizar el coche: " . $conexion->error));
            exit;
        }
    } else {
        //Crea el vehiculo si es que no habia datos en la base de datos y no las variables no tienen el valor "No aplica"
        $select_horario_proximoE = "SELECT fecha, horaInicio FROM reuniones WHERE id_Invitado = '$id_invi' ORDER BY ABS(TIMESTAMPDIFF(SECOND, TIMESTAMP(fecha, horaInicio), NOW())) LIMIT 1";
        $row = mysqli_fetch_assoc($select_horario_proximoE);    
        // Extraer los valores de fecha y horaInicio
        $fechaA = $row['fecha'];
        $horaInicioA = $row['horaInicio'];
        $insert_auto_query = "INSERT INTO auto (placa, color, modelo, id_Invitado, fecha, horaInicio) VALUES ('$nuevoCochePlaca','$nuevoCocheColor','$nuevoCocheModelo','$id_invi','$fechaA','$horaInicioA')";
        if (!mysqli_query($conexion, $insert_auto_query)) {
            echo json_encode(array("error" => "Error al Insertar el coche: " . $conexion->error));
            exit;
        }
    }

    echo json_encode(array("mensaje" => "Invitado modificado correctamente"));
}
?>
