<?php
session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json");

if(session_status()== PHP_SESSION_ACTIVE){
     // archivo de conexion a la base de datos
     include "conectar.php";
     //conexion a la base de datos a traves de la funcion conectarDB (revisar archivo conectar.php)
     $mysqli = conectarDB();
     //obtiene el JSON que se envia desde el componente login
     $datosyeison=file_get_contents("php://input");
     //convierte el JSON en un objeto de PHP
     $dataObject = json_decode($datosyeison);
     $mysqli->set_charset('utf8');
        $correo = $dataObject->correo;
        //consulta preparada para evitar inyeccion sql
        //selecciona todos los datos del usuario con el correo ingresado
        if ($nueva_consulta = $mysqli->prepare("SELECT * from usuario WHERE correo = ?")) {
            $nueva_consulta->bind_param('s', $correo);
            $nueva_consulta->execute();
            $resultado = $nueva_consulta->get_result();
            //el usuario existe
            if ($resultado->num_rows == 1) {
                $datos = $resultado->fetch_assoc();
                //Conversión de la imagen a base 64
                // $imagenData = base64_encode($datos['fotografia']);
                $foto = base64_encode($datos['fotografia']);
                //json con los datos del usuario
                echo json_encode(array('existe'=>true,'nombre'=>$datos['nombre'], 'apellidop'=>$datos['apellido_paterno'],
                'apellidom'=>$datos['apellido_materno'],'correo'=>$datos['correo'], 'foto'=>$foto));
            }
            else { //el usuario no existe
                  echo json_encode(array('existe'=>false,'conectado'=>false, 'error' => 'El usuario no existe.'));
            }
            $nueva_consulta->close();
          }//no se pudo conectar a la base de datos, la consulta no se pudo realizar
          else{
            echo json_encode(array('existe'=>false, 'error' => 'No se pudo conectar a BD'));
          }

}

?>