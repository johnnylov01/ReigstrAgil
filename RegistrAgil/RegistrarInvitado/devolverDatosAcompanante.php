<?php
include("conexion.php");

// CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json; charset=utf-8");



if ($_SERVER['REQUEST_METHOD'] === 'POST'){

    $inputJSON = file_get_contents('php://input');
   
    $input = json_decode($inputJSON, true);

    
    // Recupera el valor del campo 'correo'
    $correo = $input['correo']; // Correo del acompañante
    


    $sql3 = "SELECT * FROM usuario WHERE correo='$correo'";
    $result = $conn->query($sql3);

    if ($result->num_rows > 0) { // si el acompañante ya registró sus datos
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

   
}




?>