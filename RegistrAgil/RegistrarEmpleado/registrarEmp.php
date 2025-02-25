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

    // Verificar si se ha recibido un archivo en $_FILES
    if (isset($_FILES['fotografia'])) {
        $fotografia = $_FILES['fotografia']['tmp_name']; 
        $fotografiaContenido = addslashes(file_get_contents($fotografia));
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
    $direccion = $_POST["direccion"];
    $departamento = $_POST["departamento"];
    $permisos = $_POST['permisos'];

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

    $password = generarContraseña();

    $sql = "INSERT INTO usuario (nombre, apellido_paterno, apellido_materno, telefono, correo, empresa,   permisos, fotografia, contraseña) 
            VALUES ('$nombre', '$apaterno', '$amaterno', '$telefono', '$correo', '$empresa', '$permisos', '$fotografiaContenido', '$password')";

    // Ejecutar la consulta SQL
    if ($conn->query($sql) === TRUE) {

        // Realizar la inserción en la otra tabla 'anfitrion'
        $sql2 = "INSERT INTO anfitrion (correo, departamento, direccion) VALUES ('$correo', '$departamento', '$direccion')";

        if ($conn->query($sql2) === TRUE) {
            // Enviar una respuesta JSON con éxito
            include 'mandarContEmp.php';
            exit;
        } else {
            // Enviar una respuesta JSON con error
            echo json_encode(array("error" => "Error al insertar en tabla 'anfitrion': " . $conn->error));
        }
       
    } else {
        // Enviar una respuesta JSON con error si la consulta falla
        echo json_encode(array("error" => "Error al registrar el empleado: " . $conn->error));
    }
} else {
    // Enviar una respuesta JSON con error si no se ha enviado una solicitud POST
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}

// Cerrar la conexión a la base de datos
$conn->close();
?>
