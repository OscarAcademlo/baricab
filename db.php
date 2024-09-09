<?php
$servername = "localhost"; // Cambia esto si tu servidor MySQL no está en localhost
$username = "root"; // Reemplaza con tu nombre de usuario de MySQL
$password = ""; // Reemplaza con tu contraseña de MySQL
$dbname = "ubicaciones"; // Nombre de la base de datos que creaste

// Crear conexión
$conexion = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
