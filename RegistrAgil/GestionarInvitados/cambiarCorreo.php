<?php
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin,X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Content-Type: application/json; charset=utf-8");
    header('Access-Control-Allow-Methods: PATCH');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 3600');

    $db = [
        'host' => 'localhost',
        'port' => 3306,
        'db' => 'baseregistragil',
        'user' => 'root',
        'pass' => ''
    ];

    $dsn = "mysql:host={$db['host']};dbname={$db['db']};charset=UTF8;port={$db['port']}";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ];
    $conn = new PDO($dsn, $db['user'], $db['pass'], $options);

    if (!$conn) {
        echo json_encode(['success' => false, 'error' => 'Servicio no disponible']);
        exit();
    }

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    //Error cuando no mandan un json bien formado
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Falta el JSON']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
        if(isset($data['correo'], $data['nuevoCorreo'])) {
            $query = "UPDATE Usuario SET correo = :newcorreo WHERE correo = :correo AND permisos = 2";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':newcorreo', $data['nuevoCorreo']);
            $stmt->bindValue(':correo', $data['correo']);
            if($stmt->execute()) {
                //Aqui iria volver a mandar el correo UnU o en front que se vuelva a mandar luego de cambiar

                echo json_encode(['success' => true]);
            }else{
                echo json_encode(['success' => false, 'error' => 'Ya existe un usuario con este correo']);
            }
        }else{
            echo json_encode(['success' => false, 'error' => 'Faltan parametros']);
            exit();
        }
    }
?>