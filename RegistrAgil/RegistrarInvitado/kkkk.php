


<?php
include("conexion.php");
include 'phpqrcode/qrlib.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// CORS
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json; charset=utf-8");

function mandarCorreos($destinatarios, $enlaceFormulario) {
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';               //No cambiar
        $mail->SMTPAuth   = true;
        $mail->Username   = 'softwarelegends65@gmail.com';  //No cambiar
        $mail->Password   = 'prhj hhpo rnvs xrqj';          //No cambiar
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('softwarelegends65@gmail.com', 'Software Legends');

        foreach ($destinatarios as $destinatario) {
            $mail->addAddress($destinatario);
        }

        $mail->isHTML(true);
        $mail->Subject = 'Prueba';
        $mail->Body = '
    <html>
    <head>
        <title>Prueba de correo electrónico</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 20px auto;
                background-color: #ffffcc; /* Color de fondo */
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
                padding: 30px;
            }
            h1 {
                color: #007bff;
                text-align: center;
            }
            p {
                color: #333;
                line-height: 1.6;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>¡Hola ACOMPAÑANTE!</h1>
            <p>Este es un ejemplo de correo electrónico con diseño HTML.</p>
            <p>Puedes agregar cualquier elemento HTML aquí, como imágenes, enlaces, tablas, etc.</p>
            <p>¡Espero que esto te ayude!</p>
            <p>Da click al siguiente enlace para llevarte al formulario: <a href="'.$enlaceFormulario.'">'.$enlaceFormulario.'</a></p>
        </div>
    </body>
    </html>
';

    

        $mail->send();
        return true;
       
    } catch (Exception $e) {
        return;
        
    }
}



$acom= "si";
$acom1= "isaacresendiz480@gmail.com";
$acom2= "jsamanor8@gmail.com";


    if ($acom  === "si") {
        $correosAcompañantes = array();
        if (!empty($acom1 )) {
            $correosAcompañantes[] = $acom1;
        }
        if (!empty($acom2)) {
            $correosAcompañantes[] = $acom2;
        }

        $enlaceFormulario = "http://localhost:3000/formulario";
        if(mandarCorreos($correosAcompañantes, $enlaceFormulario)){
            echo "kooola";
            exit;
        }
        else{
            echo "jjjjjjj";
            exit;
        }
        
        
        
    }


?>