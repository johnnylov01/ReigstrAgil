<?php
// Conexion a la base 
header('Access-Control-Allow-Origin: *');

header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");

header("Content-Type: application/json; charset=utf-8");

$host = 'localhost';
$db = 'baseregistragil';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Verificar si el request es de tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los parámetros del cuerpo del request POST
    $json = file_get_contents('php://input');
    // Decodificar el JSON a un array asociativo
    $data = json_decode($json, true);

    // Verificar si se pudo decodificar el JSON correctamente
    if ($data === null) {
        // Enviar una respuesta JSON con error si hay un problema con el JSON
        echo json_encode(array("error" => "Error al decodificar el JSON"));
        exit();
    }
    $horaInicio = $data["hora_inicio"];
    $fecha = $data["fecha"];
    $sala = $data["sala"];
    $correo= $data["correo"]; 

    //Cosulta a los datos de los invitados
    $sql2 = "SELECT
            j.concepto as asunto_junta,
            ui.nombre AS invitado_nombre,
            ui.apellido_paterno AS invitado_apellido_paterno,
            ui.apellido_materno AS invitado_apellido_materno,
            ui.correo AS invitado_correo
            FROM 
            junta j
            LEFT JOIN 
            reuniones r ON j.id_Anfitrion = r.id_Anfitrion AND j.fecha = r.fecha 
            AND j.sala = r.sala and j.horaInicio = r.horaInicio
            LEFT JOIN 
            invitado i ON i.id_Invitado = r.id_Invitado
            LEFT JOIN
            usuario ui ON i.correo = ui.correo
            WHERE(j.id_Anfitrion = (SELECT id_Anfitrion FROM anfitrion WHERE correo = '$correo')
            AND j.sala = '$sala' AND j.fecha = '$fecha' AND j.horaInicio='$horaInicio');";

    // Preparar la consulta
    $stmt = $pdo->prepare($sql2);

    // Validaciones de los parámetros
    /*$stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
    $stmt->bindParam(':sala', $sala, PDO::PARAM_STR);
    $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
    $stmt->bindParam(':horaInicio', $horaInicio, PDO::PARAM_STR);*/

    // Ejecutar la consulta
    $stmt->execute();

    // Verificar si se encontraron resultados
    if ($stmt->rowCount() > 0) {
    // Crear el array para almacenar los datos de los invitados
    $invitados = [];

    // Iterar sobre los resultados y agregarlos al array
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $invitados[] = $row;
    }

    // Guardar los datos en un archivo JSON
    //file_put_contents('C:\xampp\htdocs\Proyecto\RegistrAgil\RegistrAgil\Juntas\meetings-app\public\invitados.json', json_encode($invitados, JSON_PRETTY_PRINT));

    // Enviar una respuesta JSON indicando éxito
    echo json_encode(array('invitados'=>$invitados));
    } else {
    // Enviar una respuesta JSON indicando que no se encontraron resultados
    echo json_encode(['success' => false, 'message' => 'No se encontraron datos de invitados']);
    }
} else {
    // Enviar una respuesta JSON indicando que el método de solicitud no es POST
    echo json_encode(['success' => false, 'message' => 'Método de solicitud incorrecto']);
    }

// Cerrar la conexión a la base de datos
$pdo = null;
?>