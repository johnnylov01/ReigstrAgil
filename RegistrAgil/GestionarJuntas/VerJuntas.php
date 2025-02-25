<?php
header('Access-Control-Allow-Origin: *');
// Permitir ciertos encabezados en la solicitud
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
// Establecer el tipo de contenido de la respuesta
header("Content-Type: application/json; charset=utf-8");
// Conexion a la base 
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
//Consulta a los datos de la junta
$sql1 = "SELECT DISTINCT
j.descripcion AS reunion_descripcion,
j.horaFin AS reunion_horaFin,
j.concepto AS reunion_concepto,
j.horaInicio AS reunion_horaInicio,
j.fecha AS reunion_fecha,
j.sala AS reunion_sala,
a.correo AS anfitrion_correo,
u.nombre AS anfitrion_nombre,
u.apellido_paterno AS anfitrion_apellido_paterno,
u.apellido_materno AS anfitrion_apellido_materno,
u.empresa AS anfitrion_empresa
FROM 
junta j
INNER JOIN 
anfitrion a ON j.id_Anfitrion = a.id_Anfitrion
LEFT JOIN 
reuniones r ON j.id_Anfitrion = r.id_Anfitrion AND j.fecha = r.fecha AND j.sala = r.sala
LEFT JOIN 
usuario u ON a.correo = u.correo;

";
$result1= $pdo->query($sql1);
if($result1->rowCount() > 0) {
    
    // Fetch data
    $juntas = [];
    while ($row = $result1->fetch()) {
        $juntas[] = $row;
    }
    // Escribe el archivo JSON 
    echo json_encode(array('juntas'=>$juntas));
    //file_put_contents('C:\xampp\htdocs\Proyecto\RegistrAgil\RegistrAgil\Juntas\meetings-app\public\junta.json', json_encode($juntas, JSON_PRETTY_PRINT));

    //echo 'JSON de juntas creado!';
}
else {
    echo 'No se encontraron datos de juntas!';
}


// //Cosulta a los datos de los invitados
// $sql2 = "";

// $result2= $pdo->query($sql2);
// if($result2->rowCount() > 0) {
//     echo 'JSON de invitados creado!';

//     $invitados = [];
//     while ($row = $result2->fetch()) {
//         $invitados[] = $row;
//     }
//     file_put_contents("C:\xampp\htdocs\Proyecto\RegistrAgil\RegistrAgil\Juntas\meetings-app\public\invitados.json", json_encode($invitados, JSON_PRETTY_PRINT));
// }
// else {
//     echo 'No se encontraron datos de invitados!';
// }
?>