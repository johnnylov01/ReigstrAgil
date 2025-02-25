<?php
    include "config.php";
    include "utils.php";
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Content-Type: application/json; charset=utf-8");
    header('Access-Control-Allow-Methods: GET, PATCH, PUT');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 3600'); // 1 hour cache

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }

    date_default_timezone_set('America/Mexico_City');

    if($_SERVER['REQUEST_METHOD'] === 'GET') {

        //Lectura de los headers de la peticion
        $headers = apache_request_headers();
        $isAuth = isAuth($headers, $keypass);
        
        //Error si el Token de Sesion expiro
        if($isAuth['status'] == 432) {
            header("HTTP/1.1 308 Session Expired");
            echo json_encode(['success' => false, 'error' => 'Sesion expirada']);
            exit();
        }

        //Error si no incluye el Token de Autenticacion
        if($isAuth['status'] == 401) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(['success' => false, 'error' => 'No estas autorizado']);
            exit();
        }
        
        $userData = $isAuth['payload'];

        echo json_encode(['success' => true]);
        exit();
    }

    $dbConn = connect($db);

    //Si no se pudo conectar a la base
    if(!$dbConn) {
        header("HTTP/1.1 503 Service Unavailable");
        echo json_encode(['success' => false, 'error' => 'Servicio no disponible']);
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if(!$data) {
            echo json_encode(['success' => false, 'error' => 'Falta el JSON']);
            exit();
        }

        if(isset($data['correo'])) {

            $query = "SELECT lastUpdatePass FROM Usuario WHERE correo = ?";
            $stmt = $dbConn->prepare($query);
            $stmt->bindParam(1, $data['correo']);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $date = date('Y-m-d');
                $res = $stmt->fetch();
                $diff = round( ((strtotime($date) - strtotime($res['lastUpdatePass'])) / 31556926  ) * 12 );

                if($diff > 0) {
                    if($stmt->rowCount() > 0) {
                        
                        if(sendRestorePassword($data['correo'], $keypass)) {
                            echo json_encode(['success' => true]);
                        }else{
                            echo json_encode(['success' => false, 'error' => 'No se pudo enviar el correo, intentelo mas tarde']);
                        }
                    }else{
                        echo json_encode(['success' => false, 'error' => 'No existe una cuenta con este correo']);
                    }
                }else{
                    echo json_encode(['success' => false, 'error' => 'La contraseña actual se actualizó hace menos de 1 mes.']);
                }
            }else{
                echo json_encode(['success' => false, 'error' => 'No existe una cuenta asociada a ese correo']);
            }

            
            $stmt = null;
        }else{
            echo json_encode(['success' => false, 'error' => 'Faltan parametros']);
        }
    }

    //cambiar contrasna o foto
    if($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if(!$data) {
            echo json_encode(['success' => false, 'error' => 'Falta el JSON']);
            exit();
        }

        //Lectura de los headers de la peticion
        $headers = apache_request_headers();
        $isAuth = isAuth($headers, $keypass);
        
        //Error si el Token de Sesion expiro
        if($isAuth['status'] == 432) {
            header("HTTP/1.1 308 Session Expired");
            echo json_encode(['success' => false, 'error' => 'Sesion expirada']);
            exit();
        }

        //Error si no incluye el Token de Autenticacion
        if($isAuth['status'] == 401) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(['success' => false, 'error' => 'No estas autorizado']);
            exit();
        }
        
        $userData = $isAuth['payload'];

        if(isset($data['clave'])) {
            //Validar si la contraseña no es la que ya esta en la base:
            $query = "SELECT contraseña FROM Usuario WHERE correo = ?";
            $stmt = $dbConn->prepare($query);
            $stmt->bindParam(1, $userData['correo']);
            $stmt->execute();
            
            $res = $stmt->fetch();
            $date = date('Y-m-d');

            if(!(hash('sha256', $data['clave']) == $res['contraseña'])) {
                //Cambiar contraseña
                
                $query = "UPDATE Usuario SET contraseña = :clave, lastUpdatePass = :fecha WHERE correo = :correo";
                $stmt = $dbConn->prepare($query);
                $stmt->bindValue(':clave', $data['clave']);
                $stmt->bindValue(':fecha', $date);
                $stmt->bindValue(':correo', $userData['correo']);
                if($stmt->execute()) {
                    echo json_encode(['success' => true]);
                    exit;
                }else{
                    echo json_encode(['success' => false, 'error' => 'No se pudo actualizar tu contraseña']);
                    exit;
                }
            }else{
                echo json_encode(['success' => false, 'error' => 'La contraseña nueva no puede ser igual a la contraseña actual.']);
                exit;
            }
            
            
        }

    }

    //Lectura de JSON
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    //Error cuando no mandan un json bien formado
    if(!$data) {
        echo json_encode(['success' => false, 'error' => 'Falta el JSON']);
        exit();
    }
    $dbConn = null;
?>