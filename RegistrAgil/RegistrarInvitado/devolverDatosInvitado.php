<?php
include("conexion.php");

// CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json; charset=utf-8");

function YaSeRegistro($conn, $invitado_id, $fecha, $horaInicio){
    $sql = "SELECT * FROM codigoqr 
            WHERE id_Invitado = ? AND fecha = ? and horaInicio = ?";
    
     // Prepara la declaración
     if ($stmt = $conn->prepare($sql)) {
        // Vincula los parámetros
        $stmt->bind_param("iss", $invitado_id, $fecha, $horaInicio); // "i" para entero y "s" para cadena
        
        // Ejecuta la declaración
        $stmt->execute();
        
        // Almacena el resultado
        $stmt->store_result();
        
        // Verifica si se obtuvo algún resultado
        if ($stmt->num_rows > 0) {
            
            return true; // Los valores existen en la tabla
        } else {
            return false; // Los valores no existen en la tabla
        }
        
        // Cierra la declaración
        $stmt->close();
    } else {
        
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    $inputJSON = file_get_contents('php://input');
   
    $input = json_decode($inputJSON, true);

    
    // Recupera el valor del campo 'correo'
    $correo = $input['correo']; //Correo del invitado
    $fecha = $input['fecha'];
    $horaInicio = $input['horaInicio'];


    // Obtener el ID del nuevo registro en la tabla 'invitado'
    // Tener un registro en la tabla invitado significa que ya tiene cuenta el invitado.
    $sql3 = "SELECT id_Invitado FROM invitado WHERE correo='$correo'";
    $result = $conn->query($sql3);

    if ($result->num_rows > 0) { // si el usuario ya tiene una cuenta
        $row = $result->fetch_assoc();
        $invitado_id = $row['id_Invitado'];

        // Este if revisa si ya llenó el formulario de la junta específica.
        if(YaSeRegistro($conn, $invitado_id, $fecha, $horaInicio)){
            // Preparar y ejecutar la consulta
            $sql = $conn->prepare("SELECT * FROM usuario WHERE correo = ?");
            $sql->bind_param("s", $correo); // 's' indica que el parámetro es una cadena
            $sql->execute();
            // Obtener el resultado
            $result = $sql->get_result();

            if ($result->num_rows > 0) {
                // Recorrer los resultados
                while($row = $result->fetch_assoc()) {
                    $corre = $row["correo"];
                    $name = $row["nombre"];
                    $apePat = $row["apellido_paterno"];
                    $apeMat = $row["apellido_materno"];
                    $emp = $row["empresa"];
                    $num = $row["telefono"];
                    $pic = base64_encode($row["fotografia"]);
                }
            }

            echo json_encode(array("correo"=> $corre,
                                    "nombre" => $name,
                                    "apellido_Paterno" => $apePat,
                                    "apellido_Materno" => $apeMat,
                                    "empresa" => $emp,
                                    "numero" => $num,
                                    "foto" => $pic));
            
            exit;

        }else{ // No ha usado el formulario
            echo json_encode(array("mensaje" => "no ha llenado el formulario"));
            exit;

        }      

    }else{ // el usuario no tiene una cuenta, por lo tanto no lo ha llenado
        echo json_encode(array("mensaje" => "no ha llenado el formulario"));
            exit();
    }
}




?>