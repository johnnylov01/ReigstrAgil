<?php
header('Access-Control-Allow-Origin: *');

header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");

header("Content-Type: application/json; charset=utf-8");
//colocar aqui contraseña que utilizan para gestionar sus bases de datos
$conexion = mysqli_connect("localhost", "root", "", "baseregistragil");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fecha_actual = date('Y-m-d');
    $json = file_get_contents('php://input');
    // Decodificar el JSON a un array asociativo
    $data = json_decode($json, true);

    // Verificar si se pudo decodificar el JSON correctamente
    if ($data === null) {
        // Enviar una respuesta JSON con error si hay un problema con el JSON
        echo json_encode(array("error" => "Error al decodificar el JSON"));
        exit();
    }

    $correo = $data["correo"];

    $datos = array();
    
    $id_invitado = "SELECT id_Invitado FROM invitado WHERE correo = '$correo'";
    
    $resultado_id_invitado = mysqli_query($conexion, $id_invitado);
    $fila_id_invitado = mysqli_fetch_assoc($resultado_id_invitado);
    $id_invitado = $fila_id_invitado['id_Invitado'];
    
    $usu = "SELECT nombre, apellido_paterno, apellido_materno, empresa, telefono, fotografia FROM usuario WHERE correo = '$correo'";
    $ide = "SELECT tipoIdentificacion FROM invitado WHERE correo = '$correo'";
    $eq = "SELECT NoSerie, modelo FROM dispositivo WHERE id_Invitado = '$id_invitado'";
    $vehi = "SELECT placa, color, modelo FROM auto WHERE id_Invitado = '$id_invitado'";
    $col = "SELECT id_Acompañante, correo FROM acompañante WHERE id_Invitado = '$id_invitado'";
    
    $result = mysqli_query($conexion, $usu);
    $usuario = $result->fetch_assoc();
    //Codificamos la imagen a base64
    $usuario['fotografia'] = base64_encode($usuario['fotografia']);
    
    $resultado = mysqli_query($conexion, $ide);
    $fila = mysqli_fetch_assoc($resultado);
    $identificacion = $fila['tipoIdentificacion'];
    
    $eq_res = mysqli_query($conexion, $eq);
    
    if (mysqli_num_rows($eq_res) > 0) {
        $equipo = mysqli_fetch_assoc($eq_res);
        $compu = "Si";
    } else {
        $compu = "No";
        $equipo['NoSerie'] = "No aplica";
        $equipo['modelo'] = "No aplica";
    }
    
    $vehi_res = mysqli_query($conexion, $vehi);
    
    if (mysqli_num_rows($vehi_res) > 0) {
        $vehiculo = mysqli_fetch_assoc($vehi_res);
        $coche = "Si";
    } else {
        $coche = "No";
        $vehiculo['placa'] = "No aplica";
        $vehiculo['color'] = "No aplica";
        $vehiculo['modelo'] = "No aplica";
    }
    
    $datos['usuario'] = $usuario;
    $datos['identificacion'] = $identificacion;
    $datos['equipo'] = $equipo;
    $datos['vehiculo'] = $vehiculo;
    
    $col_res = mysqli_query($conexion, $col);
    
    if ($col_res) {
        $num_filas = mysqli_num_rows($col_res);
    
        if ($num_filas > 0) {
            $i = 1;
            while ($fila = mysqli_fetch_assoc($col_res)) {
                $correo_colado = $fila['correo'];
                $col_datos = "SELECT nombre, apellido_paterno, apellido_materno FROM usuario WHERE correo = '$correo_colado'";
                $col_datos_res = mysqli_query($conexion, $col_datos);
                $row = $col_datos_res->fetch_assoc();
                $datos['acompañante'][$i-1] = array(
                    'correo' => $fila['correo'],
                    'nombre' => $row['nombre'],
                    'apellido_paterno' => $row['apellido_paterno'],
                    'apellido_materno' => $row['apellido_materno']
                );
                $i++;
            }
        } else {$datos['usuario'] = ($usuario) ? $usuario : array("nombre" => "No válido", "apellido_paterno" => "No válido", "apellido_materno" => "No válido", "empresa" => "No válido", "telefono" => "No válido");
            $datos['identificacion'] = ($identificacion) ? $identificacion : "No válido";
            $datos['equipo'] = ($equipo) ? $equipo : array("NoSerie" => "No aplica", "modelo" => "No aplica");
            $datos['vehiculo'] = ($vehiculo) ? $vehiculo : array("placa" => "No aplica", "color" => "No aplica", "modelo" => "No aplica");
            $datos['acompañante'] = ($col_res && $num_filas > 0) ? $datos['acompañante'] : array("correo" => "No válido", "nombre" => "No válido", "apellido_paterno" => "No válido", "apellido_materno" => "No válido");
        }
    } else {
        echo "Error en la consulta de correos colados: " . mysqli_error($conexion);
    }
    
    $json = json_encode(array('invitado'=>$datos));
    echo $json;
} else {
    // Enviar una respuesta JSON con error si no se ha enviado una solicitud POST
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}



?>
