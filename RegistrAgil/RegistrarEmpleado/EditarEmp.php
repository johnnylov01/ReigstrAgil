<?php

include("conexion.php");

// CORS
header('Access-Control-Allow-Origin: http://localhost:5173');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: text/plain");

function generarContraseña($longitud = 10) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $caracteresEspeciales = '!@#$%^&*()_+-=.<>?';
    $cantidadCaracteres = strlen($caracteres);
    $cantidadCaracteresEspeciales = strlen($caracteresEspeciales);
    $cadenaAleatoria = '';

    // Añadir al menos un carácter especial
    $indiceEspecialAleatorio = rand(0, $cantidadCaracteresEspeciales - 1);
    $cadenaAleatoria .= $caracteresEspeciales[$indiceEspecialAleatorio];

   
    for ($i = 1; $i < $longitud; $i++) {
        $indiceAleatorio = rand(0, $cantidadCaracteres - 1);
        $cadenaAleatoria .= $caracteres[$indiceAleatorio];
    }

    // Convertir la cadena a un array, mezclarla y volver a convertirla a una cadena
    $cadenaArray = str_split($cadenaAleatoria);
    shuffle($cadenaArray);
    $cadenaAleatoria = implode('', $cadenaArray);

    return $cadenaAleatoria;
}

// Verificar si se ha enviado una solicitud POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST["nombre"];
    $apaterno = $_POST["apellidoPat"];
    $amaterno = $_POST["apellidoMat"];
    $telefono = $_POST["telefono"];
    $correo = $_POST["correo"];
    $fotografia = $_POST["fotografia"];
    $empresa = $_POST["empresa"];
    $direccion = $_POST["direccion"];
    $departamento = $_POST["departamento"];
    $permisos = $_POST['permisos'];
    $generaNueva = $_POST['nueva'];//PARA EL INTERRUPTOR DE LA NUEVA CONTRASEÑA

    $nombreCompeltoEmp = $nombre." ".$apaterno." ".$amaterno;

    if($permisos === 'Recepcionista'){
        $permisos = 3;
    }
    else if($permisos === "Anfitrion"){
        $permisos = 4;
    }

    $sql3 = "SELECT * FROM usuario WHERE permisos='1'";
        $result = $conn->query($sql3);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $adminNombre= $row['nombre'];
            $adminp= $row['apellido_paterno'];
            $adminm= $row['apellido_materno'];
            $adminCorreo= $row['correo'];
            $adminName = $adminNombre." ".$adminp." ".$adminm;
        }

    
    if ($generaNueva == "true"){
        $password = generarContraseña();
       
        $sql = "UPDATE usuario 
            SET telefono = '$telefono',
                permisos = '$permisos',
                contraseña = '$password',
                lastUpdatePass = null
            WHERE correo = '$correo'";

    }else{
        // Preparar la consulta SQL para actualizar los datos en la tabla de empleados
        $sql = "UPDATE usuario 
        SET telefono = '$telefono',
            permisos = '$permisos'
        WHERE correo = '$correo'";
    }
    

    // Ejecutar la consulta SQL
    if ($conn->query($sql) === TRUE) {
        // Actualizar el registro en la otra tabla 'anfitrion'
        $sql2 = "UPDATE anfitrion 
                 SET departamento = '$departamento'
                 WHERE correo = '$correo'";

        if ($conn->query($sql2) === TRUE) {
            if ($generaNueva == "true"){
                include 'mandarContEmp.php';
                echo json_encode(array("message" => "Registro actualizado exitosamente en tabla usuario y anfitrión"));
                exit;
        
            }
            else{
                echo json_encode(array("message" => "Registro actualizado exitosamente en tabla usuario y anfitrión"));
            }
            // Enviar una respuesta JSON con éxito
           // echo json_encode(array("message" => "Registro actualizado exitosamente en tabla usuario y anfitrión"));
        } else {
            // Enviar una respuesta JSON con error
            echo json_encode(array("error" => "Error al actualizar en tabla 'anfitrion': " . $conn->error));
        }
       
    } else {
        // Enviar una respuesta JSON con error si la consulta falla
        echo json_encode(array("error" => "Error al actualizar el empleado: " . $conn->error));
    }
} else {
    // Enviar una respuesta JSON con error si no se ha enviado una solicitud POST
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
