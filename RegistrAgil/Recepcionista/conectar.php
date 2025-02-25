<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: text/html; charset=utf-8");
$method = $_SERVER['REQUEST_METHOD'];
//cabeceras para la respuesta de la api
function conectarDB(){
//datos de la base de datos
  $servidor = "localhost";
  $usuario = "root";
  $password = "";
  $bd = "baseregistragil";
  
    //realizamos la conexion
    $conexion = mysqli_connect($servidor, $usuario, $password,$bd);
//comprobamos si la conexion ha tenido exito
        if($conexion){
            echo "";
        }else{
            echo 'Ha sucedido un error inexperado en la conexion de la base de datos
';
        }

    return $conexion;
}
?>
