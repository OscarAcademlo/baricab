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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bariloche Cab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: #000;
        }

        .navbar-brand {
            font-size: 1.5rem;
            color: #fff;
            font-weight: bold;
        }

        .navbar-dark .navbar-nav .nav-link {
            color: #fff;
        }

        .container-main {
            display: flex;
            flex-wrap: wrap;
            padding: 20px;
        }

        .left-section {
            flex: 1;
            padding: 20px;
            max-width: 400px;
        }

        .right-section {
            flex: 2;
            height: 500px;
        }

        .footer {
            background-color: #000;
            color: #fff;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
        }

        #map {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>

<!-- Barra de navegación -->
<nav class="navbar navbar-dark navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Bariloche Cab</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Inicia sesión</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Regístrate</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Contenido principal -->
<div class="container-main">
    <!-- Sección izquierda -->
    <div class="left-section">
        <h1>Viaja a cualquier lugar con BarilocheCab</h1>
        <p>Elige tu destino y reserva tu viaje fácilmente.</p>
        <form>
            <div class="mb-3">
                <input type="text" class="form-control" placeholder="Punto de partida">
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" placeholder="Entrega">
            </div>
            <div class="mb-3">
                <select class="form-control">
                    <option>Hoy</option>
                    <option>Mañana</option>
                </select>
            </div>
            <div class="mb-3">
                <select class="form-control">
                    <option>Ahora</option>
                    <option>En 30 minutos</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary w-100">Ver precios</button>
        </form>
    </div>

    <!-- Sección derecha con mapa -->
    <div class="right-section">
        <div id="map"></div>
    </div>
</div>

<!-- Pie de página -->
<footer class="footer">
    <p>&copy; 2024 Bariloche Cab. Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Inicializar el mapa
    var map = L.map('map').setView([-41.1335, -71.3103], 13); // Coordenadas de Bariloche

    // Añadir capa de mapa
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Agregar marcadores al mapa
    var ubicaciones = <?php echo json_encode($ubicaciones); ?>;
    ubicaciones.forEach(function(ubicacion) {
        L.marker([ubicacion.latitud, ubicacion.longitud])
            .addTo(map)
            .bindPopup(ubicacion.nombre);
    });
</script>
</body>
</html>
