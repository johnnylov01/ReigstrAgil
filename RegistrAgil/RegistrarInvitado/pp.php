<?php
include("conexion.php");

$sq01 = "SELECT nombre FROM usuario WHERE correo = 'serchaaaa@gmail.com'";
    $result = $conn->query($sql01);
    // Verifica si la consulta tuvo éxito y si hay resultados
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (!is_null($row['nombre'])) { // si ya se registró el nombre, es porque ya usó el formulario
            echo "no se puede registrar más de una vez";
            exit();
        }
           
    } else {
        echo "no se encontró registro";
        exit();
    }

?>