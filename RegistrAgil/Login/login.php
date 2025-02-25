<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require 'vendor/autoload.php';

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
header("Content-Type: text/html; charset=utf-8");
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

function genToken($payload, $key) {
  return JWT::encode($payload,$key, 'HS256');
}

$method = $_SERVER['REQUEST_METHOD'];

if($method == 'OPTIONS') {
  exit();
}
  // archivo de conexion a la base de datos
    include "conectar.php";
    //conexion a la base de datos a traves de la funcion conectarDB (revisar archivo conectar.php)
    $mysqli = conectarDB();
    //sleep(1);	

  //obtiene el JSON que se envia desde el componente login
	$JSONData = file_get_contents("php://input");
  
  //convierte el JSON en un objeto de PHP
	$dataObject = json_decode($JSONData);    
    session_start();    
    $mysqli->set_charset('utf8');
    //obtiene los datos del componente login
    
	$usuario = $dataObject-> correo;
	$password =	$dataObject-> password;
  
  //consulta preparada para evitar inyeccion sql
  if ($nueva_consulta = $mysqli->prepare("SELECT correo, contraseña, permisos, lastUpdatePass from usuario WHERE correo = ?")) {
        $nueva_consulta->bind_param('s', $usuario);
        $nueva_consulta->execute();
        $resultado = $nueva_consulta->get_result();
        //el usuario existe
        if ($resultado->num_rows == 1) {
            $datos = $resultado->fetch_assoc();
             $encriptado_db = $datos['contraseña'];
             //encripta la clave del formulario y la compara con la clave encriptada de la base de datos
            if ((hash('sha256', $password))==$encriptado_db)
            {
              if(!$datos['lastUpdatePass']) {

                $payload = [
                  'correo' => $usuario,
                  'exp' => time() + 3600 //Una hora por si acaso xd
              ];
  
              $token = genToken($payload, '7z0AKw8Hxhys3RVRN7KQsoFc');

              }else{
                $token = null;
              }
              //se inicia la sesion y se guarda el correo del usuario
                $_SESSION['usuario'] = $datos['correo'];
                //se envia un JSON con la respuesta de la conexion
                echo json_encode(array('conectado'=>true, 'permiso'=>$datos['permisos'], 'bandera' => $token) );
              }

               else {
                 echo json_encode(array('conectado'=>false, 'error' => 'Usuario y/o contraseña inválidos.'));
                    }
        }
        else {
              echo json_encode(array('conectado'=>false, 'error' => 'El usuario no existe.'));
        }
        $nueva_consulta->close();
      }
      else{
        echo json_encode(array('conectado'=>false, 'error' => 'No se pudo conectar a BD'));
      }
 // }
$mysqli->close();
?>
