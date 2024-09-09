<?php
// Conectar a la base de datos
include 'db.php';

// Verificar si se ha enviado una nueva ubicación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lat']) && isset($_POST['lon']) && isset($_POST['nombre'])) {
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    $nombre = $_POST['nombre'];

    // Guardar la ubicación en la base de datos
    $stmt = $conexion->prepare("INSERT INTO ubicaciones (nombre, latitud, longitud, fecha_actualizacion) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE latitud = ?, longitud = ?, fecha_actualizacion = NOW()");
    $stmt->bind_param("sdddd", $nombre, $lat, $lon, $lat, $lon);
    $stmt->execute();
    $stmt->close();

    echo "Ubicación actualizada";
    exit;
}

// Obtener todas las ubicaciones para el seguimiento
$stmt = $conexion->prepare("SELECT * FROM ubicaciones");
$stmt->execute();
$result = $stmt->get_result();
$ubicaciones = [];
while ($row = $result->fetch_assoc()) {
    $ubicaciones[] = $row;
}
$stmt->close();

// Devolver las ubicaciones en formato JSON
header('Content-Type: application/json');
echo json_encode($ubicaciones);
?>
