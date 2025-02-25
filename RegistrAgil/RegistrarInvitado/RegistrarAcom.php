<?php
include("conexion.php");
include 'phpqrcode/qrlib.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json; charset=utf-8");

function mandarCorreo($id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $fileName, $empresa, $caducidad, $correo){
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'softwarelegends65@gmail.com'; 
        $mail->Password   = 'prhj hhpo rnvs xrqj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        // Establecer la codificación UTF-8
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('softwarelegends65@gmail.com', 'Software Legends');
        $mail->addAddress($correo);

        // Adjunta la imagen al correo y asigna un CID único
        $mail->addEmbeddedImage('logo.png', 'logo_cid', 'logo.png', 'base64', 'image/png');
        // Adjuntar imagen descargable
        $mail->addEmbeddedImage($fileName, 'qrcode_cid', 'downloadable-image.png', 'base64', 'image/png');


        $mail->isHTML(true);
        $mail->Subject = 'Confirmación de llenado de formulario';
        $mail->Body = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>RegistrÁgil</title>
                <style>
                    body {
                        font-family: sans-serif;
                        margin: 0;
                        padding: 0;
                        background-color: #f4f4f4;
                    }

                    header {
                        background-color: #88C7FF;
                        color: #fff;
                        text-align: center;
                        padding: 20px 0;
                    }

                    .container {
                        max-width: 800px;
                        margin: 0 auto;
                        padding: 20px;
                    }

                    h1 {
                        font-size: 24px;
                        margin-bottom: 20px;
                    }

                    h2 {
                        font-size: 18px;
                        margin-bottom: 10px;
                    }

                    ul {
                        list-style: none;
                        padding: 0;
                    }

                    li {
                        margin-bottom: 10px;
                    }
                    .inicio-sesion {
                    text-align: center;
                }
                    .informacion-reunion {
                        margin-bottom: 30px;
                    }

                    .registro {
                        text-align: center;
                    }
                    .mensaje{
                        text-align: center;
                    }
                    .btn-registro {
                        background-color: #88C7FF;
                        color: #fff;
                        padding: 10px 20px;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        text-decoration: none;
                        display: inline-block;
                    }

                    .btn-registro:hover {
                        background-color: #007BFF;
                    }

                    .anfitrion {
                        margin-top: 30px;
                    }

                    footer {
                        background-color: #88C7FF;
                        color: #fff;
                        text-align: center;
                        padding: 20px 0;
                    }
                </style>
            </head>
            <body>
                <header>
                    <div class="container">
                        <img src="cid:logo_cid" alt="Logo de RegistrÁgil" style="width: 200px; height: auto;">
                    </div>
                </header>

                <main>
                    <div class="container">

                        <section class="mensaje">
                            <p>Hola '.$nombreInv.',</p>
                            <p>Gracias por registrar tus datos para la reunión en '.$empresa.'. Estamos emocionados de tenerte con nosotros.</p>
                        </section>
                    
                        <section class="informacion-cuenta">
                            <h2>Código para acceso a la empresa:</h2>
                            <p>Hemos creado un código QR único para permitirte el acceso el día de la reunión. </p>

                            
                        </section>
                    
                    
                        <section class="mensaje">
                            <p>Fecha de caducidad: '.$fecha.', '.$caducidad.'hrs</p>
                            <p style="color: red;">El código QR proporcionado es único e intransferible</p>
                            <p>
                                <a href="cid:qrcode_cid" download="qrcode.png">
                                    <img src="cid:qrcode_cid" alt="Código QR" style="width: 200px; height: auto;">
                                </a>
                            </p>
                        
                            
                        </section>


                        <section class="informacion-reunion">
                            <h2>Detalles de la reunión:</h2>
                            <ul>
                                <li>Fecha: '.$fecha.'</li>
                                <li>Hora: '.$horaInicio.'</li>
                                <li>Sala: '.$sala.'</li>
                                <li>Dirección: '.$direccion.'</li>
                                <li>Anfitrión: '.$nombreCompletoAnfi.'</li>
                            </ul>
                        </section>

                
                    </div>
                </main>

                <footer>
                    <div class="container">
                        <p>&copy; 2024 RegistrÁgil</p>
                    </div>
                </footer>
            </body>
            </html>
        ';

        $mail->send();

    } catch (Exception $e) {
        echo "Error al enviar a {$destinatario}: {$mail->ErrorInfo}";
    }
}



function generaQR($id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $id_QR, $empresa, $correo){
    // Datos a meter en el QR
    $datas = $id_QR;

    // Directorio
    $dir = 'qrs/';
    
    // Nombre del archivo
    $fileName = $dir . 'qr_code_' . date("Ymd_His") . '.png';

    // Genera el QR
    QRcode::png($datas, $fileName, 'L', 4, 2);
    

    // Espera un momento para asegurarse de que el archivo se genera
    sleep(1);

    $time = $horaInicio;
    $dateTime = new DateTime($time);
    $interval = new DateInterval('PT20M');
    $dateTime->add($interval);
    $caducidad= $dateTime->format('H:i:s');

    
    

    $imagen = addslashes(file_get_contents($fileName));

    // SQL query
    $sql = "UPDATE codigoqr
            SET imagen = '$imagen',
                caducidad = '$caducidad'
            WHERE id_Codigo = '$id_QR'";
        

    // Ejecuta la consulta
    if ($conn->query($sql) === TRUE) {
        echo json_encode(array("listo" => "ya todo"));
        mandarCorreo($id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $fileName, $empresa, $caducidad, $correo);
        exit;

        
    } else {
        echo json_encode(array("error" => "Hubo problemas al registrar el correo"));
        exit();
    }

}

function guardaDatos($id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $empresa, $correo){
    //Debemos de insertar algo en la tabla porque de otro modo no tendriamos de donde sacar el id del qr
    $sql = "INSERT INTO codigoqr (sala, fecha, horaInicio, id_Anfitrion, id_Acompañante)  VALUES ('$sala', '$fecha', '$horaInicio', '$id_Anfitrion', '$id_acompa')";

    if($conn->query($sql) === TRUE){
        $id_QR = $conn->insert_id;
        generaQR($id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $id_QR, $empresa, $correo);

    }
    else{
        echo json_encode(array("error" => "Error"));
        exit();
    }

}

function registrarAutomovil($placa, $color, $amodelo, $automovil, $id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $empresa, $correo){
    if($automovil === "si"){
        //registro automóvil
        $sql0 = "INSERT INTO auto (placa, color, modelo, id_Acompañante, fecha, horaInicio) 
        VALUES ('$placa', '$color', '$amodelo', '$id_acompa', '$fecha', '$horaInicio')";
        if($conn->query($sql0) === TRUE){
            guardaDatos($id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $empresa, $correo);
            

        }else{
            echo json_encode(array("error" => "no se pudo realizar el registro, por favor revise sus datos"));
            exit;
        }
    }
    else{
        guardaDatos($id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $empresa, $correo);

    }


}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
    $sala = $_POST["sala"];
    $fecha = $_POST["fecha"];
    $horaInicio = $_POST["horaInicio"];
    $id_Anfitrion = $_POST["id_Anfitrion"];
    $id_Invitado = $_POST["id_Invitado"];
    $permisos = 2;

    $nombreInv = $nombre." ".$apaterno." "." ".$amaterno;
  

    $automovil = $_POST["automovil"];
    $amodelo = $_POST["amodelo"];
    $placa = $_POST["placa"];
    $color = $_POST["color"];




    //Obtenemos los datos de la junta
    $sql = "SELECT * FROM junta 
        WHERE sala = '$sala' AND fecha = '$fecha' AND horaInicio = '$horaInicio' AND id_Anfitrion = '$id_Anfitrion'";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) { 
        $row = $result->fetch_assoc();
        $descripcion = $row['descripcion'];
        
    }

    $sql = "SELECT * FROM anfitrion WHERE id_Anfitrion ='$id_Anfitrion'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $correoAnfitrion = $row['correo']; 
        $direccion = $row['direccion'];
    }

    //Datos
    $sql = "SELECT * FROM usuario WHERE correo ='$correoAnfitrion'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombreAnfi = $row['nombre']; 
        $apellido_pat = $row['apellido_paterno'];
        $apellido_mat = $row['apellido_materno'];
        $empresa = $row['empresa'];
        $telefono = $row['telefono'];
    }
    $nombreCompletoAnfi = $nombreAnfi." ".$apellido_pat." ".$apellido_mat;
    

    $sql = "INSERT INTO usuario (correo, nombre, apellido_paterno, apellido_materno, empresa, fotografia, telefono, permisos) VALUES ('$correo', '$nombre', '$apaterno', '$amaterno', '$empresa', '$fotografia', '$telefono', '$permisos')";
    

    if ($conn->query($sql) === TRUE) {
        $sql = "INSERT INTO acompañante(tipoIdentificacion, correo, id_Invitado, fecha, horaInicio) VALUES('$documento', '$correo', '$id_Invitado', '$fecha', '$horaInicio')";
        if ($conn->query($sql) === TRUE) {
            $id_acompa = $conn->insert_id;

            if($_POST['dispositivos'] === '0'){

                registrarAutomovil($placa, $color, $amodelo, $automovil, $id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $empresa, $correo);
                
            }
            else if($_POST['dispositivos'] === '1'){
                $modelo1 = $_POST["modelo1"];
                $serie1 = $_POST["serie1"];

                $sql4 = "INSERT INTO dispositivo (NoSerie, modelo, id_Acompañante, fecha, horaInicio) 
                VALUES ('$serie1', '$modelo1', '$id_acompa', '$fecha', '$horaInicio')";

                if ($conn->query($sql4) === TRUE) {
                    registrarAutomovil($placa, $color, $amodelo, $automovil, $id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $empresa, $correo);
                }else{
                    echo json_encode(array("error" => "no se registraron los datos"));
                    exit();
                }
                
            }else if($_POST['dispositivos'] === '2'){
                $modelo1 = $_POST["modelo1"];
                $serie1 = $_POST["serie1"];
                $modelo2 = $_POST["modelo2"];
                $serie2 = $_POST["serie2"];

                $sql4 = "INSERT INTO dispositivo (NoSerie, modelo, id_Acompañante, fecha, horaInicio) 
                VALUES ('$serie1', '$modelo1', '$id_acompa', '$fecha', '$horaInicio')";
                
                if ($conn->query($sql4) === TRUE) {
                    $sql5 = "INSERT INTO dispositivo (NoSerie, modelo, id_Acompañante, fecha, horaInicio) 
                            VALUES ('$serie2', '$modelo2', '$id_acompa', '$fecha', '$horaInicio')";
                    
                    if ($conn->query($sql5) === TRUE) {

                        registrarAutomovil($placa, $color, $amodelo, $automovil, $id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $empresa, $correo);
    
                    }else{
                        echo json_encode(array("error" => "Error al insertar los datos"));
                        exit();
                    }
                }else{
                    echo json_encode(array("error" => "Error al insertar los datos"));
                    exit();
                }
            }else if($_POST['dispositivos'] === '3'){
                $modelo1 = $_POST["modelo1"];
                $serie1 = $_POST["serie1"];
                $modelo2 = $_POST["modelo2"];
                $serie2 = $_POST["serie2"];
                $modelo3 = $_POST["modelo3"];
                $serie3 = $_POST["serie3"];

                $sql4 = "INSERT INTO dispositivo (NoSerie, modelo, id_Acompañante, fecha, horaInicio) 
                VALUES ('$serie1', '$modelo1', '$id_acompa', '$fecha', '$horaInicio')";
                
                if ($conn->query($sql4) === TRUE) {
                    $sql5 = "INSERT INTO dispositivo (NoSerie, modelo, id_Acompañante, fecha, horaInicio) 
                            VALUES ('$serie2', '$modelo2', '$id_acompa', '$fecha', '$horaInicio')";
                    
                    if ($conn->query($sql5) === TRUE) {
                        $sql6 = "INSERT INTO dispositivo (NoSerie, modelo, id_Acompañante, fecha, horaInicio) 
                        VALUES ('$serie3', '$modelo3', '$id_acompa', '$fecha', '$horaInicio')";

                        if($conn->query($sql6) === TRUE){

                            registrarAutomovil($placa, $color, $amodelo, $automovil, $id_acompa, $fecha, $horaInicio, $conn,  $id_Anfitrion, $nombreCompletoAnfi, $nombreInv, $sala, $direccion, $descripcion, $empresa, $correo);

                        }else{
                            echo json_encode(array("error" => "Error al insertar los datos"));
                            exit();

                        }   
                    }else{
                        echo json_encode(array("error" => "Error al insertar los datos"));
                        exit();
                    }
                }else{
                    echo json_encode(array("error" => "Error al insertar los datos"));
                    exit();
                }
            }



            echo json_encode(array("message" => "Nuevo registro creado exitosamente"));
            exit();
        }else{
            echo json_encode(array("error" => "No se pudo registrar"));
            exit();
        }
    }
    else{
        echo json_encode(array("error" => "No se pudo registrar"));
            exit();
    }


}else{
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}



?>