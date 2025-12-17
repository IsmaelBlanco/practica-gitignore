<?php
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

$contactoJson = file_get_contents(__DIR__ . '/../data/info.json');
$contacto = json_decode($contactoJson, true);

$latitud = $contacto['coordenadas']['lat'];
$longitud = $contacto['coordenadas']['lng'];

$telefono = $contacto['telefono'];
$mail = $contacto['mail'];

$nominatimUrl = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$latitud}&lon={$longitud}&zoom=18&addressdetails=1";

$options = [
    "http" => [
        "header" => "User-Agent: CarSolucion/1.0\r\n"
    ]
];
$context = stream_context_create($options);
$respuesta = file_get_contents($nominatimUrl, false, $context);
$datosDireccion = json_decode($respuesta, true);

$direccion = $datosDireccion['address'];

$calle = $direccion['road'] ?? '';
$numero = $direccion['house_number'] ?? '';
$barrio = $direccion['neighbourhood'] ?? '';
$cp = $direccion['postcode'] ?? '';
$ciudad = $direccion['city'] ?? ($direccion['town'] ?? '');
$provincia = $direccion['state'] ?? '';

$parteIzquierda = '';
if ($calle) {
    $parteIzquierda .= $calle;
    if ($numero) {
        $parteIzquierda .= " $numero";
    }
}
if ($barrio) {
    $parteIzquierda .= " ($barrio)";
}

$parteDerecha = trim("$cp $ciudad ($provincia)");

$direccionFormateada = trim($parteIzquierda);
if ($parteDerecha) {
    if ($direccionFormateada) {
        $direccionFormateada .= " · ";
    }
    $direccionFormateada .= $parteDerecha;
}

if (empty($direccionFormateada)) {
    $direccionFormateada = 'Dirección no disponible';
}

//Horarios
$lunes = $contacto['horario']['lunes'];
$martes = $contacto['horario']['martes'];
$miercoles = $contacto['horario']['miercoles'];
$jueves = $contacto['horario']['jueves'];
$viernes = $contacto['horario']['viernes'];
$sabado = $contacto['horario']['sabado'];
$domingo = $contacto['horario']['domingo'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/src/logo.png" type="image/png">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/infostyle.css">
    <link href="https://cdn.jsdelivr.net/npm/open-dyslexic@1.0.0/open-dyslexic.css" rel="stylesheet">
    <script src="/js/accessibility.js" defer></script>
    <script src="../js/bootstrap.bundle.min.js" defer></script>
    <title>Información | CarSolucion </title>
</head>

<body>
    <!-- Boton de Whatsapp -->
    <div class="whatsappapi">
        <a href="https://api.whatsapp.com/send?phone=34611365892&text=Hola%2C%20quiero%20más%20información%20sobre%20vuestros%20vehículos"
            class="whatsapp-button" target="_blank" rel="noopener noreferrer">
            <img src="/src/whatsapp-icon.png" alt="WhatsApp" />
        </a>
    </div>
    <!-- Panel de accesibilidad -->
    <div id="accessibility-panel" class="accessibility">
        <button id="toggle-accessibility"><img src="/src/accessibility.png"></button>
        <div id="accessibility-options" class="hidden">
            <label for="theme-select">Tema:</label>
            <select id="theme-select">
                <option value="dark">Oscuro</option>
                <option value="light">Claro</option>
            </select>

            <label for="font-size">Tamaño de fuente:</label>
            <input type="range" id="font-size" min="12" max="24" value="16">

            <label for="font-select">Fuente:</label>
            <select id="font-select">
                <option value="default">Por defecto</option>
                <option value="serif">Serif</option>
                <option value="dyslexic">Dyslexia Friendly</option>
            </select>
            <button id="reset-accessibility">Restablecer ajustes</button>
        </div>
    </div>
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
            <a href="/pages/informacion">Información</a>
        </div>
    </header>

    <main class="pginfo">
        <div class="infocontent">
            <div class="ftr infomap">
                <iframe frameborder="0" style="border:0" allowfullscreen loading="lazy"
                    src="https://maps.google.com/maps?q=<?php echo $latitud; ?>,<?php echo $longitud; ?>&hl=es&z=14&output=embed">
                </iframe>
            </div>
            <div class="ftr infolocation">
                <h1>Localización</h1>
                <p><?php echo htmlspecialchars($direccionFormateada); ?></p>
                <img src="/src/location.png">
            </div>
            <div class="ftr infohours">
                <h1>Horario</h1>
                <ul>
                    <li>
                        <p>Lunes:</p>
                        <p><?= $lunes ?></p>
                    </li>
                    <hr>
                    <li>
                        <p>Martes:</p>
                        <p><?= $martes ?></p>
                    </li>
                    <hr>
                    <li>
                        <p>Miércoles:</p>
                        <p><?= $miercoles ?></p>
                    </li>
                    <hr>
                    <li>
                        <p>Jueves:</p>
                        <p><?= $jueves ?></p>
                    </li>
                    <hr>
                    <li>
                        <p>Viernes:</p>
                        <p><?= $viernes ?></p>
                    </li>
                    <hr>
                    <li>
                        <p>Sábado:</p>
                        <p><?= $sabado ?></p>
                    </li>
                    <hr>
                    <li>
                        <p>Domingo:</p>
                        <p><?= $domingo ?></p>
                    </li>
                </ul>
                <img src="/src/schedule.png">
            </div>
            <div class="contact-area">
                <div class="ftr infocontact">
                    <h1>Contacto</h1>
                    <p><img class="ifc" src="/src/tlfn.png"> <?= $telefono ?></p>
                    <p><img class="ifc" src="/src/mail.png"> <?= $mail ?></p>
                </div>
                <div class="ftr sendmail">
                    <h1>Envíanos un mail</h1>
                    <form action="/php/mail.php" method="post">
                        <label for="nombre">Nombre:</label><br>
                        <input type="text" id="nombre" name="nombre" required><br><br>

                        <label for="email">Correo electrónico:</label><br>
                        <input type="email" id="email" name="email" required><br><br>

                        <label for="mensaje">Mensaje:</label><br>
                        <textarea id="mensaje" name="mensaje" rows="5" required></textarea><br><br>

                        <button type="submit">Enviar</button>
                    </form>
                </div>
            </div>
        </div>
        <div id="toast"></div>
    </main>
    <footer class="footer">
        &copy; <span id="year"></span> CarSolucion. Todos los derechos reservados.
    </footer>
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();

        const params = new URLSearchParams(window.location.search);
        const status = params.get('status');
        const toast = document.getElementById('toast');

        if (status === 'success') {
            toast.textContent = "Mensaje enviado correctamente";
            toast.style.backgroundColor = "#28a745"; // verde
            toast.classList.add('show');
        } else if (status === 'error') {
            toast.textContent = "Error al enviar el mensaje";
            toast.style.backgroundColor = "#dc3545"; // rojo
            toast.classList.add('show');
        }

        if (toast.classList.contains('show')) {
            setTimeout(() => {
                toast.classList.remove('show');
                // Opcional: limpiar el parámetro de la URL para que no se muestre más al recargar
                history.replaceState(null, '', window.location.pathname);
            }, 3000);
        }
    </script>
</body>

</html>