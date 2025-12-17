<?php
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
require_once "../php/conexion.php";

if (!($isAdmin)) {
    header('Location: /');
    exit;
}


$jsonData = json_decode(file_get_contents('../data/info.json'), true);
$telefono = $jsonData['telefono'];
$mail = $jsonData['mail'];
$horario = $jsonData['horario'];
$lat = $jsonData['coordenadas']['lat'];
$lng = $jsonData['coordenadas']['lng'];

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/src/logo.png" type="image/png">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/edtinfstyle.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="../js/bootstrap.bundle.min.js" defer></script>
    <title>Editar información | CarSolucion </title>
</head>

<body>

    <header>
        <div class="header_content">
            <div class="left">
                <img class="logo" src="/src/logo.png" alt="Logo">
            </div>

            <div class="right">
                <a href="/">Inicio</a>
                <a href="/pages/catalogo">Vehículos</a>
                <a href="/pages/informacion">Contacto</a>
                <?php if ($isAdmin): ?>
                    <a href="/pages/admin-area">Área de administración</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="nav">
            <a href="/"><img src="/src/home.png">Inicio</a>
            <svg class="rghtarrw" width="15px" height="15px" viewBox="-7.58 -7.58 90.96 90.96"
                xmlns="http://www.w3.org/2000/svg" fill="#000000" stroke="#000000" stroke-width="7.5804">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <g id="Group_65" data-name="Group 65" transform="translate(-831.568 -384.448)">
                        <path id="Path_57" data-name="Path 57"
                            d="M833.068,460.252a1.5,1.5,0,0,1-1.061-2.561l33.557-33.56a2.53,2.53,0,0,0,0-3.564l-33.557-33.558a1.5,1.5,0,0,1,2.122-2.121l33.556,33.558a5.53,5.53,0,0,1,0,7.807l-33.557,33.56A1.5,1.5,0,0,1,833.068,460.252Z"
                            fill="#ffffff"></path>
                    </g>
                </g>
            </svg>
            <a href="/pages/admin-area">Área de administración</a>
            <svg class="rghtarrw" width="15px" height="15px" viewBox="-7.58 -7.58 90.96 90.96"
                xmlns="http://www.w3.org/2000/svg" fill="#000000" stroke="#000000" stroke-width="7.5804">
                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                <g id="SVGRepo_iconCarrier">
                    <g id="Group_65" data-name="Group 65" transform="translate(-831.568 -384.448)">
                        <path id="Path_57" data-name="Path 57"
                            d="M833.068,460.252a1.5,1.5,0,0,1-1.061-2.561l33.557-33.56a2.53,2.53,0,0,0,0-3.564l-33.557-33.558a1.5,1.5,0,0,1,2.122-2.121l33.556,33.558a5.53,5.53,0,0,1,0,7.807l-33.557,33.56A1.5,1.5,0,0,1,833.068,460.252Z"
                            fill="#ffffff"></path>
                    </g>
                </g>
            </svg>
            <a href="/pages/edtinf">Editar información</a>
        </div>
    </header>

    <main>
        <h2>Editar Información de Contacto</h2>
        <form method="POST" action="/php/edtinf.php">
            <div id="inputs">
                <div id="chgtfno" class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" id="telefono" name="telefono" class="form-control"
                        value="<?= htmlspecialchars($telefono) ?>" required>
                </div>
                <div id="chgmail" class="mb-3">
                    <label for="mail" class="form-label">Correo electrónico</label>
                    <input type="email" id="mail" name="mail" class="form-control"
                        value="<?= htmlspecialchars($mail) ?>" required>
                </div>
                    <hr>
                <h2>Ubicación</h2>
                <div id="chglat" class="mb-3">
                    <label for="lat" class="form-label">Latitud</label>
                    <input type="text" id="lat" name="lat" class="form-control" value="<?= htmlspecialchars($lat) ?>"
                        required>
                </div>
                <div id="chglng" class="mb-3">
                    <label for="lng" class="form-label">Longitud</label>
                    <input type="text" id="lng" name="lng" class="form-control" value="<?= htmlspecialchars($lng) ?>"
                        required>
                </div>
            </div>

            <div id="dias">
                <h2>Horario</h2>
                <?php foreach ($horario as $dia => $hora): ?>
                    <div id="chghor<?= $dia ?>" class="mb-2">
                        <label for="<?= $dia ?>" class="form-label"><?= ucfirst($dia) ?></label>
                        <input type="text" id="<?= $dia ?>" name="horario[<?= $dia ?>]" class="form-control"
                            value="<?= htmlspecialchars($hora) ?>" required>
                    </div>
                <?php endforeach; ?>
            </div>


            <div id="map"></div>

            <button class="sbbtn" type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </main>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div id="infoToast" class="toast align-items-center text-bg-success border-0 show" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Información actualizada correctamente
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Cerrar"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const latInput = document.getElementById('lat');
            const lngInput = document.getElementById('lng');

            const initialLat = parseFloat(latInput.value) || 0;
            const initialLng = parseFloat(lngInput.value) || 0;

            const map = L.map('map').setView([initialLat, initialLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
            }).addTo(map);

            const marker = L.marker([initialLat, initialLng], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function (e) {
                const pos = marker.getLatLng();
                latInput.value = pos.lat.toFixed(6);
                lngInput.value = pos.lng.toFixed(6);
            });

            function updateMarkerFromInputs() {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                if (!isNaN(lat) && !isNaN(lng)) {
                    marker.setLatLng([lat, lng]);
                    map.setView([lat, lng], map.getZoom());
                }
            }

            latInput.addEventListener('input', updateMarkerFromInputs);
            lngInput.addEventListener('input', updateMarkerFromInputs);
        });
        window.addEventListener('DOMContentLoaded', () => {
            const toastEl = document.getElementById('infoToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
                toast.show();
            }
        });
    </script>
</body>

</html>