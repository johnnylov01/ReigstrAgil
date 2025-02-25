<?php
include("conexion.php");
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function cifrar($datos, $llave){
    $mensaje = json_encode($datos);
    $inivec = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
    $men_encriptado = openssl_encrypt($mensaje, "AES-256-CBC", $llave, 0, $inivec);
    return base64_encode($men_encriptado . "::" . $inivec);

}

function mandarCorreos($destinatario, $enlaceFormulario, $fecha, $hora, $sala, $descripcion, $direccion, $nombreCompleto, $correoAnfitrion, $telefono, $empresa) {
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
                        <p>Es un placer para nosotros informarte que estás invitado a una reunión en '.$empresa.'. </p>

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

        // if(!$mail->send()){
        //     echo json_encode(['success' => false, 'message' => 'Error al enviar el correo a ' . $destinatario . ': ' . $mail->ErrorInfo]);
        //     exit;
        // }

        $mail->clearAddresses();
        // echo 'Enviado correctamente a ' . $destinatario;
        //json de confirmación
        // echo json_encode(array('success' => true, 'message' => 'Correo enviado correctamente'));
        // exit;
    } catch (Exception $e) {
        // echo "Error al enviar a {$destinatario}: {$mail->ErrorInfo}";
        //json de error
        // echo json_encode(array('success' => false, 'message' => 'Error al enviar el correo'));
        // exit;
    }
}

// ***************************************************************************** Modificar a partir de aqui*****


$data = json_decode(file_get_contents("php://input"));

$destinatarios = $data->destinatarios;
$hora_inicio = $data->hora_inicio;
$fecha = $data->fecha;
$sala = $data->sala;
$descripcion = $data->descripcion;
$correo_anfitrion = $data->correo_anfitrion;
$direccion = $data->direccion;
// $destinatarios = $data['destinatarios'];
// $hora_inicio = $data['hora_inicio'];
// $fecha = $data['fecha'];
// $sala = $data['sala'];
// $descripcion = $data['descripcion'];
// $correo_anfitrion = $data['correo_anfitrion'];

//Añadir a en el array a todos los invitados de la junta
// $destinatarios = array(
//     'isaacresendiz480@gmail.com',
//     'jsamanor8@gmail.com'
// );

//Llave para encriptar los datos del enlace (NO CAMBIAR)
$llave = 'softwareLegendsEsGOD';

// Array de datos que se mandarán como parámetros inicialmente vacío
$dataArray = array();


// $id_Anfitrion = 2;
// $sala = 'Sala 4D'; // Estos datos cambian dependiendo la junta que se crea
// $fecha = '2024-06-13';
// $hora = '09:00:00';
// $descripcion = 'Plan de marketing';
// $cantidadDeAcompanantes = 'si'; //Incluir el dato de cuantos acompañantes tiene permitido llevar (PONER NÚMERO EN LUGAR DE SI O NO)

//Cn el id del anfitrion buscamos los demas campos a mostrar en la invitación
$sql = "SELECT * FROM anfitrion WHERE correo ='$correo_anfitrion'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $IDAnfitrion = $row['id_Anfitrion'];
    // $direccion = $row['direccion'];
}

$sql = "SELECT * FROM usuario WHERE correo ='$correo_anfitrion'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nombreAnfi = $row['nombre'];
    $apellido_pat = $row['apellido_paterno'];
    $apellido_mat = $row['apellido_materno'];
    $empresa = $row['empresa'];
    $telefono = $row['telefono'];
}

$nombreCompleto = $nombreAnfi." ".$apellido_pat." ".$apellido_mat;

//Recorremos el array de los destinatarios
foreach ($destinatarios as $destinatario) {
    $dataArray['sala'] = $sala; // Estos datos cambian dependiendo la junta que se crea
    $dataArray['fecha'] = $fecha;
    $dataArray['horaInicio'] = $hora_inicio;
    $dataArray['id_Anfitrion'] = $IDAnfitrion;
    $dataArray['correo'] = $destinatario; //No cambiar este
    //Buscamos los id de los invitados en la tabla invitado con sus correos
    $sql = "SELECT * FROM invitado WHERE correo ='$destinatario'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_Invitado = $row['id_Invitado']; //ID de invitado
    }
    //Buscamos la cantidad de acompañantes de los invitados en la tabla reuniones, con los campos de la junta recibidos y el id de los invitados y del anfitrion
    $sql = "SELECT * FROM reuniones WHERE id_Anfitrion = '$IDAnfitrion' AND fecha = '$fecha' AND sala = '$sala' AND horaInicio = '$hora_inicio' AND id_Invitado = '$id_Invitado'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cantidadDeAcompanantes = $row['numAcompañantes']; //Cantidad de acompañantes
    }
    $dataArray['cantidadDeAcompanantes'] = $cantidadDeAcompanantes;
    $encrypted_data = cifrar($dataArray, $llave);
    $enlaceFormulario = "http://localhost:5173/FormularioInvitado?data=" . urlencode($encrypted_data);



    mandarCorreos($destinatario, $enlaceFormulario, $fecha, $hora_inicio, $sala, $descripcion, $direccion, $nombreCompleto, $correo_anfitrion, $telefono, $empresa);
    //json de confirmación
    // echo json_encode(array('success' => true, 'message' => 'Correo enviado correctamente'));
}
//Envio del json de confirmación
echo json_encode(array('success' => true, 'message' => 'Invitaciones enviadas correctamente'));
$conn->close();
exit;

?>
