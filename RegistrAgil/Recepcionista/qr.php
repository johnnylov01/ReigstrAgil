<?php

session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json");

//funcion para buscar en la base de datos.
function buscarDB($tabla, $columna, $codigo) {
    // Asegúrate de que $mysqli esté disponible dentro de la función
    global $mysqli;
    if ($nueva_consulta = $mysqli->prepare("SELECT * from $tabla WHERE $columna = ?")) {
        $nueva_consulta->bind_param('s', $codigo);
        $nueva_consulta->execute();
        $resultado = $nueva_consulta->get_result();
        //el usuario existe
        if ($resultado->num_rows == 1) {
            $datos = $resultado->fetch_assoc();
            //arreglo con los datos del usuario
            return $datos;
        }
        else { //el campo no existe
            return array('existe'=>false,'conectado'=>false, 'error' => 'El campo no existe.');
        }
        //$nueva_consulta->close();
    }//no se pudo conectar a la base de datos, la consulta no se pudo realizar
    else{
        // return json_encode(array('existe'=>false, 'error' => 'No se pudo conectar a BD'));
        return array('existe' => false, 'error' => 'No se pudo conectar a BD');
    }
}
function buscarauto($idInvitado, $fecha)
{
    global $mysqli;
    $sql="SELECT color, placa, modelo  FROM auto WHERE id_Invitado = '$idInvitado' and fecha = '$fecha'";
    $resultado = $mysqli->query($sql);
    if($resultado->num_rows == 1){
        $datos = $resultado->fetch_assoc();
    }
    else
        $datos=null;


    return $datos;
}
function disps($idInvitado, $fecha)
{
    global $mysqli;
    $sql="SELECT modelo, NoSerie FROM dispositivo WHERE id_Invitado = '$idInvitado' and fecha = '$fecha'";
    $resultado = $mysqli->query($sql);
    if($resultado->num_rows >=1){
        while($datos = $resultado->fetch_assoc()){
            $dispositivos[]=$datos;
        }
    }
    else
        $dispositivos=null;
    return $dispositivos;
}

function datosjunta($fecha, $horain, $anfi)
{
    global $mysqli;
    $sql="SELECT concepto, horaFin from junta WHERE fecha = '$fecha' and horaInicio = '$horain' and id_Anfitrion = '$anfi'";
    $resultado = $mysqli->query($sql);
    if($resultado->num_rows == 1){
        $datos = $resultado->fetch_assoc();
    }
    else
        $datos=null;
    return $datos;
}
try{
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
            $codigo = $dataObject->id_qr;
            // echo json_encode(array('codigo'=>$codigo));
            // exit;
            //consulta preparada para evitar inyeccion sql

            if($qr=buscarDB('codigoqr', 'id_Codigo', $codigo))
            {
                //datos de la junta
                $juntafun=datosjunta($qr['fecha'], $qr['horaInicio'], $qr['id_Anfitrion']);

                $junta=array('caducidad'=>$qr['caducidad'], 'sala'=>$qr['sala'], 'horaInicio'=>$qr['horaInicio'], 'fecha'=>$qr['fecha'],
                'horaFin'=>$juntafun['horaFin'], 'concepto'=>$juntafun['concepto']);
                // echo json_encode(array('junta'=>$junta));
                // exit;
                $id_Invitado=$qr['id_Invitado'];
                $id_Anfitrion=$qr['id_Anfitrion'];
                //comprobar si hay invitado registrado con el id del invitado
                if($inv=buscarDB('invitado', 'id_Invitado', $id_Invitado))
                {
                    $correo_invitado=$inv['correo'];
                    if($userInvitado=buscarDB('usuario', 'correo', $correo_invitado))
                    {
                        $foto_base64 = base64_encode($userInvitado['fotografia']);
                        $invitado=array("nombre"=>$userInvitado['nombre'], "apellido_paterno"=>$userInvitado['apellido_paterno'], "apellido_materno"=>$userInvitado['apellido_materno'], "correo"=>$userInvitado['correo'],
                         "empresa"=>$userInvitado['empresa'],
                         "foto" => $foto_base64,
                        "telefono"=>$userInvitado['telefono'], 'Identificacion'=>$inv['tipoIdentificacion']);
                        // echo json_encode(array('invitado'=>$invitado));
                        // exit;
                        $datosauto=buscarauto($qr['id_Invitado' ], $qr['fecha']);
                        $dispositivos=disps($qr['id_Invitado'], $qr['fecha']);

                        // echo json_encode(array('auto'=>$datosauto));
                        // exit;
                    }else{
                        //no existe usuario con el correo del invitado
                        $invitado=null;
                    }
                    //echo json_encode(array('correo_invitado'=>$invitado['correo']));
                }else{
                    // echo json_encode(array("Error"=>$qr['id_Invitado']));
                    echo json_encode(array("error" => "El invitado no existe."));
                    exit;
                }
                //comprobar si hay anfitrion registrado con el id del anfitrion
                if($anfi=buscarDB('anfitrion','id_Anfitrion', $id_Anfitrion ))
                {
                    $correo_anfitrion=$anfi['correo'];
                    if($userAnfitrion=buscarDB('usuario', 'correo', $correo_anfitrion))
                    {
                        $anfitrion=array("nombre"=>$userAnfitrion['nombre'], "apellido_paterno"=>$userAnfitrion['apellido_paterno'],
                        "apellido_materno"=>$userAnfitrion['apellido_materno'],
                        "empresa"=>$userAnfitrion['empresa']);

                    }else{
                        //no existe usuario con el correo del anfitrion
                        $anfitrion=null;
                    }
                }else{
                    //no existe anfitrion con el id del anfitrion
                    $anfitrion=null;
                }
                echo json_encode(array('invitado'=>$invitado, 'auto'=>$datosauto, 'anfitrion'=>$anfitrion, 'junta'=>$junta, 'dispositivos'=>$dispositivos));

                

                //echo json_encode(array('id_Invitado'=>$qr['id_Invitado']));
            }else{
                echo json_encode(array('error'=>'No existe el qr ingresado'));
            }
            
            
            
            
            
            
        // if ($nueva_consulta = $mysqli->prepare("SELECT * from codigoqr WHERE id_Codigo = ?")) {
        //     $nueva_consulta->bind_param('s', $codigo);
        //     $nueva_consulta->execute();
        //     $resultado = $nueva_consulta->get_result();
        //     //el usuario existe
        //     if ($resultado->num_rows == 1) {
        //         $datos = $resultado->fetch_assoc();
        //         //json con los datos del usuario
        //         echo json_encode(array('existe'=>true,'codigoqr'=>$datos['id_Codigo'], 'id_Invitado'=>$datos['id_Invitado']));
        //     }
        //     else { //el usuario no existe
        //           echo json_encode(array('existe'=>false,'conectado'=>false, 'error' => 'El usuario no existe.'));
        //     }
        //     $nueva_consulta->close();
        //   }//no se pudo conectar a la base de datos, la consulta no se pudo realizar
        //   else{
        //     echo json_encode(array('existe'=>false, 'error' => 'No se pudo conectar a BD'));
        //   }
        //consulta preparada para evitar inyeccion sql
        //selecciona todos los datos del usuario con el correo ingresado
        /*$qr=verdatos('codigoqr', $codigo, 'id_Codigo',  $mysqli);
        if($qr){
            echo json_encode(array('existe'=>true));
        }*/
        /*if($qr!=null){
            //el qr existe
            $idInvitado=$qr['id_Invitado'];
            $idAnfitrion=$qr['id_Anfitrion'];
            if($inv=verdatos('invitado', $idInvitado,'id_Invitado', $mysqli)){
                //el invitado dxiste
                $mensaje="invitado existe";
                $correoinv=$inv['correo'];
                //comprueba si hay usuario registrado con el correo del invitado
                if($datosInv=verdatos('usuario', $correoinv, 'correo', $mysqli)){
                    $invitado=array("nombre"=>$datosInv['nombre'], "apellido_paterno"=>$datosInv['apellido_paterno'], "apellido_materno"=>$datosInv['apellido_materno'], "correo"=>$datosInv['correo'],
                     "foto"=>$datosInv['fotografia'], "empresa"=>$inv['empresa'],
                      "telefono"=>$inv['telefono']);
                    if($auto=verdatos('auto', $idInvitado, 'id_Invitado', $mysqli)){
                        $datosauto=array("modelo"=>$auto['modelo'], "placa"=>$auto['placa'], "color"=>$auto['color']);
                    }else{
                        $datosauto= null;
                    }
                }else{
                    $invitado=null;
                }
            }else{
                $mensaje="invitado inexistente";
            }
            //comprueba si hay usuario registrado con el correo del anfitrion
            if($anfitrion=verdatos('anfitrion', $idAnfitrion, 'id_Anfitrion', $mysqli))
            {
                $correoanfi=$anfitrion['correo'];
                if($anfi=verdatos('usuario', $correoanfi, 'correo', $mysqli)){
                    $anfitrion=array("nombre"=>$anfi['nombre'], "apellido_paterno"=>$anfi['apellido_paterno'],
                     "apellido_materno"=>$anfi['apellido_materno'],
                      "correo"=>$anfi['correo'], "foto"=>$anfi['fotografia'],
                       "empresa"=>$anfitrion['empresa'], "telefono"=>$anfitrion['telefono']);
                }else{
                    $anfitrion=null;
                }
            }
        }else{
            $mensajeqr="QR inexistente";
        }*/
    }
}catch(Exception $e){
    echo json_encode(array('error'=>$e->getMessage()));
}
exit;


    //echo json_encode(array("mensaje"=>$mensaje, "invitado"=>$invitado, "auto"=>$datosauto, "anfitrion"=>$anfitrion, "mensajeqr"=>$mensajeqr))
    //echo json_encode(array("mensaje"=>$mensaje));


?>