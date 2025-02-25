<?php
$servername = "localhost";
$database = "baseregistragil";
$username = "root";
//colocar aqui contraseña que utilizan para gestionar sus bases de datos
$password = "";

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Content-Type: text/html; charset=utf-8");

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
    }

?>