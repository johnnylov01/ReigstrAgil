<?php
session_start();
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: application/json");
function idInv($correo)
{
    global $mysqli;
    $sql="SELECT * FROM usuario where correo='".$correo."'";
    $resultado=$mysqli->query($sql);
    if($resultado->num_rows>0)
    {
        //si existe, se liga a la reunión
        $sql="SELECT * FROM invitado where correo='".$correo."'";
                $resultado=$mysqli->query($sql);
                if($resultado->num_rows>0)
                {
                    $datos=$resultado->fetch_assoc();
                    $idUsuario= $datos['id_Invitado'];
                }else{
                    $insercionInv="INSERT INTO invitado (correo) VALUES ('".$correo."')";
                    $resultado=$mysqli->query($insercionInv);
                    if($mysqli->affected_rows>0)
                    {
                        $sql="SELECT * FROM invitado where correo='".$correo."'";
                        $resultado=$mysqli->query($sql);
                        $datos=$resultado->fetch_assoc();
                        $idUsuario= $datos['id_Invitado'];
                    }else{
                        $idUsuario=null;
                    }
                }
    }else{
        //si no existe, se crea y se liga a la reunión
        $insercionUs="INSERT INTO usuario (correo, permisos) VALUES ('".$correo."', 2)";
        $mysqli->query($insercionUs);
        if($mysqli->affected_rows>0)
        {
            $insercionInv="INSERT INTO invitado (correo) VALUES ('".$correo."')";
            $resultado=$mysqli->query($insercionInv);
            if($mysqli->affected_rows>0)
            {
                $sql="SELECT * FROM invitado where correo='".$correo."'";
                $resultado=$mysqli->query($sql);
                $datos=$resultado->fetch_assoc();
                $idUsuario= $datos['id_Invitado'];
            }else{
                $idUsuario=null;
            }
        }
    }

    return $idUsuario;
}

function idAnf($correo)
{
    global $mysqli;
    $sql="SELECT * FROM usuario where correo='".$correo."'";

    $resultado=$mysqli->query($sql);
    if($resultado->num_rows>0)
    {
        //si existe, se liga a la reunión
        $sql="SELECT * FROM anfitrion where correo='".$correo."'";
                $resultado=$mysqli->query($sql);
                $datos=$resultado->fetch_assoc();
                $idAnfitrion= $datos['id_Anfitrion'];

    }
    return $idAnfitrion;

}

try {
    // archivo de conexion a la base de datos
    include "conectar.php";
    //conexion a la base de datos a traves de la funcion conectarDB (revisar archivo conectar.php)
    $mysqli = conectarDB();
    if(session_status() == PHP_SESSION_ACTIVE)
    {
        //obtiene el JSON que se envia desde el componente login
        $datosyeison=file_get_contents("php://input");
        //convierte el JSON en un objeto de PHP
        $dataObject = json_decode($datosyeison);
        $mysqli->set_charset('utf8');
        $horaInicio=$dataObject->hora_inicio;
        $fecha=$dataObject->fecha;
        $sala=$dataObject->sala;
        $correo_anfitrion=$dataObject->correo_anfitrion;
        $correo_invitado=$dataObject->correo_invitado;
        $acomp=$dataObject->acompanantes;
        $idInvitado=idInv($correo_invitado);
        $idAnfitrion=idAnf($correo_anfitrion);

        $ligaReunion="INSERT INTO reuniones (id_invitado, sala, fecha, horaInicio, id_Anfitrion, numAcompañantes) VALUES ('".$idInvitado."','".$sala."','".$fecha."','".$horaInicio."','".$idAnfitrion."','".$acomp."')";
        $mysqli->query($ligaReunion);
        if($mysqli->affected_rows>0)
        {
            echo json_encode(array('Ligada'=> true));
        }else{
            echo json_encode(array('Ligada'=> false));
        }

    }
} catch (Exception $e) {
    echo json_encode(array("Ligada" => "No cayo en el try"));
}
?>