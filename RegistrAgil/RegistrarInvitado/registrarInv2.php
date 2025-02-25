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

function  confirmacionNuevo($destinatario, $fecha, $hora, $sala,  $direccion, $nombreCompleto, $empresaAnfi, $nombreInv, $contra) {
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
        $mail->addAddress($destinatario);

        // Adjunta la imagen al correo y asigna un CID único
        $mail->addEmbeddedImage('logo.png', 'logo_cid', 'logo.png', 'base64', 'image/png');

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
                        background-color: #0B1215;
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
                        color: #0B1215;
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
                        background-color: #0B1215;
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
                            <p>Gracias por registrar tus datos para la reunión en '.$empresaAnfi.'. Estamos emocionados de tenerte con nosotros.</p>
                        </section>
                    
                        <section class="informacion-cuenta">
                            <h2>Información de tu cuenta:</h2>
                            <p>Hemos creado una cuenta para ti para que puedas acceder a nuestra plataforma y descargar tu código QR de acceso.</p>
                            <ul>
                            <li>Usuario: '.$destinatario.'</li>
                            <li>Contraseña: '.$contra.'</li>
                            </ul>
                        </section>
                    
                    
                        <section class="inicio-sesion">
                            <p>Inicia sesión en RegistrÁgil para descargar tu código QR. Deberás presentar este código en la entrada el día de la reunión.</p>
                            <a href="http://localhost:5173/LogIn" class="btn-registro">INICIAR SESIÓN</a>
                        </section>


                        <section class="informacion-reunion">
                            <h2>Detalles de la reunión:</h2>
                            <ul>
                                <li>Fecha: '.$fecha.'</li>
                                <li>Hora: '.$hora.'</li>
                                <li>Sala: '.$sala.'</li>
                                <li>Dirección: '.$direccion.'</li>
                                <li>Anfitrión: '.$nombreCompleto.'</li>
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
        
        echo json_encode(array("error" => "Error al enviar el correo: {$mail->ErrorInfo}"));
        exit;
    }
}

function confirmacion($destinatario, $fecha, $hora, $sala, $direccion, $nombreCompleto, $empresaAnfi, $nombreInv) {
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
        $mail->addAddress($destinatario);

        // Adjunta la imagen al correo y asigna un CID único
        $mail->addEmbeddedImage('logo.png', 'logo_cid', 'logo.png', 'base64', 'image/png');

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
                        background-color: #0B1215;
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
                        color: #0B1215;
                        padding: 10px 20px;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        text-decoration: none;
                        display: inline-block;
                    }

                    .btn-registro:hover {
                        background-color: #0B1215;
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
                            <p>Gracias por registrar tus datos para la reunión en '.$empresaAnfi.'. Estamos emocionados de tenerte con nosotros.</p>
                        </section>
                    
                        <section class="informacion-cuenta">
                            <h2>Información de tu cuenta:</h2>
                            <p>Hemos notado que ya tienes una cuenta con nosotros.</p>
                            
                        </section>
                    
                    
                        <section class="inicio-sesion">
                            <p>Inicia sesión en RegistrÁgil para descargar tu código QR. Deberás presentar este código en la entrada el día de la reunión.</p>
                            <a href="http://localhost:5173/LogIn" class="btn-registro">INICIAR SESIÓN</a>
                        </section>


                        <section class="informacion-reunion">
                            <h2>Detalles de la reunión:</h2>
                            <ul>
                                <li>Fecha: '.$fecha.'</li>
                                <li>Hora: '.$hora.'</li>
                                <li>Sala: '.$sala.'</li>
                                <li>Dirección: '.$direccion.'</li>
                                <li>Anfitrión: '.$nombreCompleto.'</li>
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

function cifrar($datos, $llave){
    $mensaje = json_encode($datos);
    $inivec = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
    $men_encriptado = openssl_encrypt($mensaje, "AES-256-CBC", $llave, 0, $inivec);
    return base64_encode($men_encriptado."::".$inivec);

}


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


function generaQR($conn, $id_QR, $tieneCuenta, $data, $contra, $nombreInv) {
    
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

    $time = $data['horaInicio'];
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
        
        
        if($tieneCuenta == true){
            echo json_encode(array("message" => "Registrado correctamente"));
            confirmacion($data['correo'], $data['fecha'], $data['horaInicio'], $data['sala'],  $data['direccion'], $data['nombreCompleto'], $data['empresaAnfi'], $nombreInv);
            exit;
        }else{
            echo json_encode(array("message" => "Registrado correctamente"));
            
            confirmacionNuevo($data['correo'], $data['fecha'], $data['horaInicio'], $data['sala'],  $data['direccion'], $data['nombreCompleto'], $data['empresaAnfi'], $nombreInv, $contra);
            exit;
            

        }
       
    } else {
        echo json_encode(array("error" => "Hubo problemas al registrar el correo"));
        exit();
    }
}



function guardaDatosQR($conn, $invitado_id, $sala, $fecha, $horaInicio, $id_Anfitrion, $tieneCuenta, $data, $contra, $nombreInv){
    //Debemos de insertar algo en la tabla porque de otro modo no tendriamos de donde sacar el id del qr
    $sql = "INSERT INTO codigoqr (caducidad, escaneado, imagen, sala, fecha, horaInicio, id_Anfitrion, id_Invitado)  VALUES ('00:00:00','0','','$sala', '$fecha', '$horaInicio', '$id_Anfitrion', '$invitado_id')";

    if($conn->query($sql) === TRUE){
        $id_QR = $conn->insert_id;
        generaQR($conn, $id_QR, $tieneCuenta, $data, $contra, $nombreInv);
    }
    else{
        echo json_encode(array("error" => "Error"));
        exit();
    }


}

function registrarAutomovil($automovil, $amodelo, $placa, $color, $invitado_id, $conn, $sala, $fecha, $horaInicio, $id_Anfitrion, $tieneCuenta, $data, $contra, $nombreInv){
    
    if($automovil === "si"){
        //registro automóvil
        $sql0 = "INSERT INTO auto (placa, color, modelo, id_Invitado, fecha, horaInicio) 
        VALUES ('$placa', '$color', '$amodelo', '$invitado_id', '$fecha', '$horaInicio')";
        if($conn->query($sql0) === TRUE){
            guardaDatosQR($conn, $invitado_id, $sala, $fecha, $horaInicio, $id_Anfitrion, $tieneCuenta, $data, $contra, $nombreInv);

            exit();

        }else{
            echo json_encode(array("error" => "no se pudo realizar el registro, por favor revise sus datos"));
        }


    }
    else{
        guardaDatosQR($conn, $invitado_id, $sala, $fecha, $horaInicio, $id_Anfitrion, $tieneCuenta, $data, $contra, $nombreInv);

    }
}

function valoresExistentes($conn, $invitado_id, $fecha, $horaInicio){
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

function mandarCorreos($destinatario, $enlaceFormulario, $fecha, $hora, $sala, $descripcion,$direccion, $nombreCompleto, $correoAnfitrion, $telefono, $empresaAnfi) {
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
        $mail->addAddress($destinatario);

        // Adjunta la imagen al correo y asigna un CID único
        $mail->addEmbeddedImage('logo.png', 'logo_cid', 'logo.png', 'base64', 'image/png');

        $mail->isHTML(true);
        $mail->Subject = 'Invitación a reunión';
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
                        background-color: #0B1215;
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
                        color: #0B1215;
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
                        background-color: #0B1215;
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
                        <p>Es un placer para nosotros informarte que estás invitado a una reunión en '.$empresaAnfi.'. </p>
            
                        </section>


                        <section class="informacion-reunion">
                            <h2>Detalles de la reunión:</h2>
                            <ul>
                                <li>Fecha: '.$fecha.'</li>
                                <li>Hora: '.$hora.'</li>
                                <li>Sala: '.$sala.'</li>
                                <li>Dirección: '.$direccion.'</li>
                                <li>Anfitrión: '.$nombreCompleto.'</li>
                            </ul>
                        </section>

                        <section class="asunto-reunion">
                            <h2>Asunto de la Reunión</h2>
                            <ul>
                                <li>'.$descripcion.'</li>
                            </ul>
                        </section>

                        <section class="registro">
                            <h2>Registro previo:</h2>
                            <p>Para prepararnos para tu visita y facilitar tu acceso a nuestras instalaciones, es necesario realizar tu registro previo en el siguiente enlace:</p>
                            <a href="'.$enlaceFormulario.'" class="btn-registro">REGISTRAR</a>
                            <p>Al completar tu registro, recibirás un usuario y contraseña para ingresar a [RegistrÁgil] y descargar tu código QR de acceso. Este código es necesario para entrar al edificio el día de la reunión.</p>
                        </section>

                        <section class="anfitrion">
                            <h2>Anfitrión:</h2>
                            <ul>
                                <li>Nombre: '.$nombreCompleto.'</li>
                                <li>Correo electrónico: '.$correoAnfitrion.'</li>
                                <li>Teléfono: '.$telefono.'</li>
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_FILES['fotografia'])) { //'foto' es el atributo name del formulario
        $fotografia = $_FILES['fotografia']['tmp_name']; 
        $fotografiaContenido = addslashes(file_get_contents($fotografia));
        // $fotografia_data = file_get_contents($fotografia); 
        // $fotografia_base64 = base64_encode($fotografia_data); // Codificar el contenido en base64
    } else {
        // Enviar una respuesta JSON con error si no se recibió la imagen
        echo json_encode(array("error" => "No se recibió la imagen"));
        exit();
    }

    $nombre = $_POST["nombre"];
    $apaterno = $_POST["apaterno"];
    $amaterno = $_POST["amaterno"];
    $telefonoInv = $_POST["telefono"];
    $correo = $_POST["correo"];
    $empresa = $_POST["empresa"];
    $documento = $_POST["documento"];
    $sala = $_POST["sala"];
    $fecha = $_POST["fecha"];
    $horaInicio = $_POST["horaInicio"];
    $id_Anfitrion = $_POST["id_Anfitrion"];
    $contra = generarContraseña();

    $nombreInv = $nombre." ".$apaterno." "." ".$amaterno;
  

    $automovil = $_POST["automovil"];
    $amodelo = $_POST["amodelo"];
    $placa = $_POST["placa"];
    $color = $_POST["color"];

    $llave = 'softwareLegendsEsGOD';


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

    $sql = "SELECT * FROM usuario WHERE correo ='$correoAnfitrion'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombreAnfi = $row['nombre']; 
        $apellido_pat = $row['apellido_paterno'];
        $apellido_mat = $row['apellido_materno'];
        $empresaAnfi = $row['empresa'];
        $telefono = $row['telefono'];
    }
    $nombreCompleto = $nombreAnfi." ".$apellido_pat." ".$apellido_mat;

    $destinatarios = array();

   
    //MANDAR LOS CORREOS A LOS ACOMPAÑANTES
    if ($_POST["acompañantes"] == 1) {
        $destinatarios[] = $_POST["correoAcompañante1"];
    }
    elseif($_POST["acompañantes"] ==2){
        $destinatarios[] = $_POST["correoAcompañante1"];
        $destinatarios[] = $_POST["correoAcompañante2"];
        
    }

    

    
    //Revisamos si el invitado tiene cuenta
    // Obtener el ID del nuevo registro en la tabla 'invitado'
    $sql3 = "SELECT id_Invitado FROM invitado WHERE correo='$correo'";
    $result = $conn->query($sql3);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $invitado_id = $row['id_Invitado'];

        

        $data = array();
        $data['sala'] = $sala; // Estos datos cambian dependiendo la junta que se crea
        $data['fecha'] = $fecha;
        $data['horaInicio'] = $horaInicio;
        $data['id_Anfitrion'] = $id_Anfitrion;
        $data['id_Invitado'] = $invitado_id;
        //Recorremos el array de los destinatarios
        foreach ($destinatarios as $destinatario) {            
            $data['correo'] = $destinatario; //No cambiar este
            $encrypted_data = cifrar($data, $llave);
            $enlaceFormulario = "http://localhost:5173/FormularioAcompañante?data=" . urlencode($encrypted_data);
            

            mandarCorreos($destinatario, $enlaceFormulario, $fecha, $horaInicio, $sala, $descripcion, $direccion, $nombreCompleto, $correoAnfitrion, $telefono, $empresaAnfi);
        }
        $data['descripcion'] = $descripcion;
        $data['direccion'] = $direccion;
        $data['nombreCompleto'] = $nombreCompleto;
        $data['empresaAnfi'] = $empresaAnfi;

        
        $data['correo'] = $correo;
        
        //Revisamos si ya tiene cuenta creada
        $sql3 = "SELECT nombre FROM usuario WHERE correo='$correo'";
        $result = $conn->query($sql3);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $nombreUsuario= $row['nombre'];
        }else{
            $nombreUsuario = NULL;
        }

        if($nombreUsuario == NULL){
            $tieneCuenta = false;
        }
        else{
            $tieneCuenta = true;
        }
        //--------------------------------------
        //Checamos si ya tiene cuenta, si sí, hacemos update sin la contraseña, si no hacemos update con la contraseña
        if($tieneCuenta == true){
            $sql = "UPDATE usuario 
            SET nombre = '$nombre', 
                apellido_paterno = '$apaterno', 
                apellido_materno = '$amaterno', 
                telefono = '$telefonoInv', 
                empresa = '$empresa', 
                permisos = 2,      
                fotografia = '$fotografiaContenido'
            WHERE correo = '$correo'";
        }else{
            $sql = "UPDATE usuario 
            SET nombre = '$nombre', 
                apellido_paterno = '$apaterno', 
                apellido_materno = '$amaterno', 
                telefono = '$telefonoInv', 
                empresa = '$empresa', 
                permisos = 2, 
                fotografia = '$fotografiaContenido',
                contraseña = '$contra'        
            WHERE correo = '$correo'";
        }
            if ($conn->query($sql) === TRUE) {
                //ahora en la tabla del invitado
                $sql2 = "UPDATE invitado 
                    SET tipoIdentificacion = '$documento'           
                    WHERE correo = '$correo'";

                if ($conn->query($sql2) === TRUE) {

                            //Ahora obtenemos lo de los dispositivos
                            if($_POST['dispositivos'] === '0'){

                                //echo json_encode(array("message" => "Nuevo registro creado exitosamente en tabla usuario y invitado"));
                                //exit();
                                registrarAutomovil($automovil, $amodelo, $placa, $color, $invitado_id, $conn, $sala, $fecha, $horaInicio, $id_Anfitrion, $tieneCuenta, $data, $contra, $nombreInv);
                                
                            }
                            else if($_POST['dispositivos'] === '1'){
                                $modelo1 = $_POST["modelo1"];
                                $serie1 = $_POST["serie1"];

                                $sql4 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado, fecha, horaInicio) 
                                VALUES ('$serie1', '$modelo1', '$invitado_id', '$fecha', '$horaInicio')";

                                if ($conn->query($sql4) === TRUE) {
                                    registrarAutomovil($automovil, $amodelo, $placa, $color, $invitado_id, $conn, $sala, $fecha, $horaInicio, $id_Anfitrion, $tieneCuenta, $data, $contra, $nombreInv);
                                }else{
                                    echo json_encode(array("error" => "no se registraron los datos"));
                                    exit();
                                }
                                
                            }else if($_POST['dispositivos'] === '2'){
                                $modelo1 = $_POST["modelo1"];
                                $serie1 = $_POST["serie1"];
                                $modelo2 = $_POST["modelo2"];
                                $serie2 = $_POST["serie2"];

                                $sql4 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado, fecha, horaInicio) 
                                VALUES ('$serie1', '$modelo1', '$invitado_id', '$fecha', '$horaInicio')";
                                
                                if ($conn->query($sql4) === TRUE) {
                                    $sql5 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado, fecha, horaInicio) 
                                            VALUES ('$serie2', '$modelo2', '$invitado_id', '$fecha', '$horaInicio')";
                                    
                                    if ($conn->query($sql5) === TRUE) {

                                        registrarAutomovil($automovil, $amodelo, $placa, $color, $invitado_id, $conn, $sala, $fecha, $horaInicio, $id_Anfitrion, $tieneCuenta, $data, $contra, $nombreInv);
                    
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

                                $sql4 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado, fecha, horaInicio) 
                                VALUES ('$serie1', '$modelo1', '$invitado_id', '$fecha', '$horaInicio')";
                                
                                if ($conn->query($sql4) === TRUE) {
                                    $sql5 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado, fecha, horaInicio) 
                                            VALUES ('$serie2', '$modelo2', '$invitado_id', '$fecha', '$horaInicio')";
                                    
                                    if ($conn->query($sql5) === TRUE) {
                                        $sql6 = "INSERT INTO dispositivo (NoSerie, modelo, id_Invitado, fecha, horaInicio) 
                                        VALUES ('$serie3', '$modelo3', '$invitado_id', '$fecha', '$horaInicio')";

                                        if($conn->query($sql6) === TRUE){

                                            registrarAutomovil($automovil, $amodelo, $placa, $color, $invitado_id, $conn, $sala, $fecha, $horaInicio, $id_Anfitrion, $tieneCuenta, $data, $contra, $nombreInv);

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

                }
                else{
                    echo json_encode(array("error" => "Error al registrar el invitado: " . $conn->error));
                }
            }else{
                echo json_encode(array("error" => "Error al registrar el invitado: " . $conn->error));
            }

    }
    else {
        echo json_encode(array("error" => "Error al registrar el invitado: " . $conn->error));

    }

    





}else{
    echo json_encode(array("error" => "No se ha enviado una solicitud POST"));
}



?>
