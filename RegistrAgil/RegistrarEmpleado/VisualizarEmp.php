
<?php

include "conexion.php";

// CORS
header('Access-Control-Allow-Origin: *');

header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");

header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $json = file_get_contents('php://input');
    
    $data = json_decode($json, true);

    // Verificar si se pudo decodificar el JSON correctamente
    if (!$data) {
        // Enviar una respuesta JSON con error si hay un problema con el JSON
        echo json_encode(array("error" => "Error al decodificar el JSON"));
        exit();
    }

    $email = $data["correo"];
    
    //si es un correo nada mas, hacemos esto

    $query = "SELECT e.nombre, e.correo, e.apellido_paterno as apellidoPat, e.apellido_materno as apellidoMat, e.empresa, e.fotografia, e.telefono, e.permisos, a.direccion, a.departamento FROM Usuario as e LEFT JOIN anfitrion as a ON e.correo = a.correo WHERE e.correo = '$email' AND e.permisos > 2";
    $stmt = $conn->prepare($query);
    if($stmt){
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            // Obtener la fila del resultado
            $row = $resultado->fetch_assoc();

          
            //$fotografia_base64 = base64_encode($row['fotografia']);
            if(!$row['direccion']) {
                $row['departamento'] = 'Recepción';
                $row['direccion'] = null;
            }

            switch($row['permisos'] ){
                case 3: 
                    $row['permisos'] = "Recepcionista";
                    break;
                case 4: 
                    $row['permisos'] = "Anfitrion";
                    break;
            }

            //Imagen a base64
            $row['fotografia'] = base64_encode($row['fotografia']);
           
            echo json_encode($row);
    
            $stmt->close();
            $conn->close();
   
        } else {
            echo json_encode(array("error" => "Empleado no encontrado"));
        }
    }else{
        echo json_encode(array("error" => "consulta errónea"));
    }
}
    
?>
