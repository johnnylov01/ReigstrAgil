<?php
require 'vendor/autoload.php';

// Configuración de encabezados CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Cambia esto por tu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username   = 'softwarelegends65@gmail.com';
    $mail->Password   = 'prhj hhpo rnvs xrqj';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->CharSet = 'UTF-8';
    // Configuración del correo
    $mail->setFrom('softwarelegends65@gmail.com', 'Software Legends');
    $mail->addAddress($correo);

    $mail->addEmbeddedImage('logo.png', 'logo_cid', 'logo.png', 'base64', 'image/png');

    $mail->isHTML(true);
    $mail->Subject = 'Bienvenido a ' . $empresa;
    $mail->Body = '<!DOCTYPE html>
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
                    .mensaje{
                        text-align: center;
                    }
                    .inicio-sesion {
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
                        <h2>Hola ' . $nombreCompeltoEmp . '</h2>
                        <section class="mensaje">
                            <p>Nos complace darle la bienvenida a ' . $empresa . '. Estamos encantados de que se haya unido a nuestro equipo.</p>
                            <h2>Información de tu cuenta:</h2>
                            <p>A continuación, encontrará su usuario y contraseña para iniciar sesión en RegistrÁgil:</p>
                            <ul>
                                <li>Usuario: ' . $correo . '</li>
                                <li>Contraseña: '.$password.'</li>
                            </ul>
                            <section class="inicio-sesion">
                                <a href="http://localhost:5173/Login" class="btn-registro">INICIAR SESIÓN</a>
                            </section>
                            <p>Atentamente,<br>' . $adminName . '<br>' . $adminCorreo . '</p>
                        </section>
                    </div>
                </main>

                <footer>
                    <div class="container">
                        <p>&copy; 2024 RegistrÁgil</p>
                    </div>
                </footer>
            </body>
            </html>';

    $mail->send();
    //echo "Enviado correctamentge";
    // echo json_encode(['success' => true, 'message' => 'Correo enviado correctamente']);
    
} catch (Exception $e) {
    // echo json_encode(['success' => false, 'message' => 'No se pudo enviar el correo. Error: ' . $mail->ErrorInfo]);
    //echo "No se puede enviar";
}
?>
