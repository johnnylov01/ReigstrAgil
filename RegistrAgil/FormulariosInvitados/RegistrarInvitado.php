<?php
include("conexion.php");

// CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
     // Verificar si se ha recibido un archivo en $_FILES
     if (isset($_FILES['fotografia'])) { //'foto' es el atributo name del formulario
        $fotografia = $_FILES['fotografia']['tmp_name']; 
        $fotografia_data = file_get_contents($fotografia); 
        $fotografia_base64 = base64_encode($fotografia_data); // Codificar el contenido en base64
    } else {
        // Enviar una respuesta JSON con error si no se recibió la imagen
        echo json_encode(array("error" => "No se recibió la imagen"));
        exit();
    }

    $nombre = $_POST["nombre"];
    $apaterno = $_POST["apaterno"];
    $amaterno = $_POST["amaterno"];
    $telefono = $_POST["telefono"];
    $correo = $_POST["correo"];
    $empresa = $_POST["empresa"];
    $documento = $_POST["documento"];
    $contra = 123;

   

    $sql = "INSERT INTO usuario (nombre, apellido_paterno, apellido_materno, telefono, correo, empresa,   permisos, fotografia, contraseña) 
            VALUES ('$nombre', '$apaterno', '$amaterno', '$telefono', '$correo', '$empresa', 2, '$fotografia_base64', '$contra')";

    if ($conn->query($sql) === TRUE) {
    //ahora en la tabla del invitado
    $sql2 = "INSERT INTO invitado (correo, tipoIdentificacion) VALUES ('$correo', '$documento')";
        if ($conn->query($sql2) === TRUE) {

            // Obtener el ID del nuevo registro en la tabla 'invitado'
            $sql3 = "SELECT id_Invitado FROM invitado WHERE correo='$correo'";
            $result = $conn->query($sql3);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $invitado_id = $row['id_Invitado']; // Obtengo el ID de la tabla invitado}

                //Ahora obtenemos lo de los dispositivos
                if($_POST['dispositivos'] === '0'){
                    echo json_encode(array("message" => "Nuevo registro creado exitosamente en tabla usuario y invitado"));
                     exit();
                }
                else if($_POST['dispositivos'] === '1'){
                    $modelo1 = $_POST["modelo1"];
                    $serie1 = $_POST["serie1"];

                    $sql4 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado) 
                      VALUES ('$serie1', '$modelo1', '$invitado_id')";

                    if ($conn->query($sql4) === TRUE) {
                        echo json_encode(array("message" => "se registro tambien el dispositivo", "idinvitadio" => $invitado_id));
                        exit();
                    }else{
                        echo json_encode(array("message" => "NOO"));
                        exit();
                    }
                    
                }else if($_POST['dispositivos'] === '2'){
                    $modelo1 = $_POST["modelo1"];
                    $serie1 = $_POST["serie1"];
                    $modelo2 = $_POST["modelo2"];
                    $serie2 = $_POST["serie2"];

                    $sql4 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado) 
                      VALUES ('$serie1', '$modelo1', '$invitado_id')";
                    
                    if ($conn->query($sql4) === TRUE) {
                        $sql5 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado) 
                                   VALUES ('$serie2', '$modelo2', '$invitado_id')";
                        
                        if ($conn->query($sql5) === TRUE) {
                            echo json_encode(array("message" => "se registro tambien los dos dispositivos", "idinvitadio" => $invitado_id));
                            exit();
          
                        }else{
                            echo json_encode(array("message" => "Error al insertar los datos"));
                            exit();
                        }
                    }else{
                        echo json_encode(array("message" => "Error al insertar los datos"));
                        exit();
                    }
                }else if($_POST['dispositivos'] === '3'){
                    $modelo1 = $_POST["modelo1"];
                    $serie1 = $_POST["serie1"];
                    $modelo2 = $_POST["modelo2"];
                    $serie2 = $_POST["serie2"];
                    $modelo3 = $_POST["modelo3"];
                    $serie3 = $_POST["serie3"];

                    $sql4 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado) 
                      VALUES ('$serie1', '$modelo1', '$invitado_id')";
                    
                    if ($conn->query($sql4) === TRUE) {
                        $sql5 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado) 
                                   VALUES ('$serie2', '$modelo2', '$invitado_id')";
                        
                        if ($conn->query($sql5) === TRUE) {
                            $sql6 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado) 
                            VALUES ('$serie3', '$modelo3', '$invitado_id')";

                            if($conn->query($sql6) === TRUE){
                                echo json_encode(array("message" => "se registro tambien los tres dispositivos", "idinvitadio" => $invitado_id));
                                exit();

                            }else{
                                echo json_encode(array("message" => "Error al insertar los datos"));
                                 exit();

                            }   
                        }else{
                            echo json_encode(array("message" => "Error al insertar los datos"));
                            exit();
                        }
                    }else{
                        echo json_encode(array("message" => "Error al insertar los datos"));
                        exit();
                    }
                }

            } else {
                echo json_encode(array("error" => "No se pudo obtener el ID del invitado"));
            }
        
            
        }else{
            echo json_encode(array("error" => "Error al registrar el invitado: " . $conn->error));
        }


    }else{
        echo json_encode(array("error" => "Error al registrar el invitado: " . $conn->error));
    }

 



}else{
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}

$conn->close();
?>