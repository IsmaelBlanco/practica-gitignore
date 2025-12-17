<?php
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

date_default_timezone_set("Europe/Madrid");

$fecha = date("Y-m-d H:i:s");
$ip = $_SERVER['REMOTE_ADDR'];
$pagina = $_SERVER['REQUEST_URI'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$log = "$fecha | IP: $ip | Página: $pagina | Navegador: $userAgent" . PHP_EOL;

file_put_contents("visitas.log", $log, FILE_APPEND);

//Informacion de contacto:
$contactoJson = file_get_contents(__DIR__ . '/data/info.json');
$contacto = json_decode($contactoJson, true);

$tlf = $contacto['telefono'];
$mail = $contacto['mail'];

//Ubicacion
$latitud = $contacto['coordenadas']['lat'];
$longitud = $contacto['coordenadas']['lng'];

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
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="Página principal de CarSolucion">
    <meta name="robots" content="index, follow">
    <link rel="icon" href="/src/logo.png" type="image/png">
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/indexstyle.css">
    <link href="https://cdn.jsdelivr.net/npm/open-dyslexic@1.0.0/open-dyslexic.css" rel="stylesheet">
    <script src="/js/accessibility.js" defer></script>
    <script src="/js/redirlogin.js" defer></script>
    <script src="/js/bootstrap.bundle.min.js" defer></script>
    <title>Inicio | CarSolucion </title>
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
                <img id="adminLogo" class="logo" src="/src/logo.png" alt="Logo"
                    onclick="window.location.href='/pages/admin-access.php'">
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
        </div>
    </header>

    <main>
        <div id="CarouselMain" class="carousel slide">
            <!-- <div class="carousel-indicators">
                <button type="button" data-bs-target="#CarouselMain" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#CarouselMain" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#CarouselMain" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div> -->
            <!-- Considerar:
                 <picture>
                    <source media="(max-width: 768px)" srcset="img-mobile.jpg">
                    <source media="(min-width: 769px)" srcset="img-desktop.jpg">
                    <img src="img-default.jpg" alt="Descripción">
                </picture>                                                          -->
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="src/maincarousel1.png" class="img-fluid" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="src/maincarousel2.png" class="img-fluid" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="src/maincarousel1.png" class="img-fluid" alt="...">
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#CarouselMain" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#CarouselMain" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        <div class="maincontent">
            <div class="info">
                <h1><u>Bienvenido a CarSolucion</u></h1>
                <p>
                    Tu confianza sobre ruedas en Cantabria. Nos dedicamos a la compraventa de vehículos de ocasión,
                    ofreciendo una
                    cuidada selección de coches revisados, garantizados y listos para salir a la carretera.
                </p>
                <p>
                    Contamos con una amplia variedad de marcas, modelos y precios para que encuentres el coche que mejor
                    se adapta a ti.
                    Nuestro equipo te asesora sin compromiso, resolviendo cualquier duda con honestidad y cercanía.
                </p>
                <h2>¿Qué ofrecemos?</h2>
                <div class="features">
                    <div class="feature-card">
                        <img src="/src/financiacion.png" alt="Icono Financiación" />
                        <hr />
                        <p>Financiación a medida</p>
                    </div>
                    <div class="feature-card">
                        <img src="/src/documentacion.png" alt="Icono Documentación" />
                        <hr />
                        <p>Tramitación completa de la documentación</p>
                    </div>
                    <div class="feature-card">
                        <img src="/src/garantia.png" alt="Icono Garantía" />
                        <hr />
                        <p>Garantía incluida en todos nuestros vehículos</p>
                    </div>
                </div>

                <p>
                    Visítanos en nuestras instalaciones o consulta nuestro catálogo online actualizado.
                    En CarSolucion, tu próximo coche te está esperando.
                </p>
            </div>
            <hr>
            <div class="maincontact">
                <div class="map">
                    <iframe frameborder="0" style="border:0" allowfullscreen loading="lazy"
                        src="https://maps.google.com/maps?q=<?php echo $latitud; ?>,<?php echo $longitud; ?>&hl=es&z=14&output=embed">
                    </iframe>
                </div>
                <div class="infohor">
                    <div class="table-wrapper">
                        <table class="table-horarios">
                            <thead>
                                <tr>
                                    <th colspan="2">Horarios de atención</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Lunes</td>
                                    <td><?= htmlspecialchars($lunes) ?></td>
                                </tr>
                                <tr>
                                    <td>Martes</td>
                                    <td><?= htmlspecialchars($martes) ?></td>
                                </tr>
                                <tr>
                                    <td>Miércoles</td>
                                    <td><?= htmlspecialchars($miercoles) ?></td>
                                </tr>
                                <tr>
                                    <td>Jueves</td>
                                    <td><?= htmlspecialchars($jueves) ?></td>
                                </tr>
                                <tr>
                                    <td>Viernes</td>
                                    <td><?= htmlspecialchars($viernes) ?></td>
                                </tr>
                                <tr>
                                    <td>Sábado</td>
                                    <td><?= htmlspecialchars($sabado) ?></td>
                                </tr>
                                <tr>
                                    <td>Domingo</td>
                                    <td><?= htmlspecialchars($domingo) ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <hr>
            <div class="infocards">
                <div class="spanmoreinfo">
                    <p>Contáctanos en...</p>
                    <a href="/pages/informacion">
                        <li>Más información</li>
                    </a>
                </div>
                <div class="infoftrs">
                    <div class="ftrcard">
                        <img src="/src/tlfn.png">
                        <hr>
                        <p><?= htmlspecialchars($tlf) ?></p>
                    </div>
                    <div class="ftrcard">
                        <img src="/src/mail.png">
                        <hr>
                        <p><?= htmlspecialchars($mail) ?></p>
                    </div>
                </div>

            </div>
        </div>
    </main>
    <footer class="footer">
        &copy; <span id="year"></span> CarSolucion. Todos los derechos reservados.
    </footer>
    <script>
        //resalta todos los "CarSolucion"
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.querySelector('.info');
            const textoBuscado = 'CarSolucion';
            if (!container) return;

            const regex = new RegExp(textoBuscado, 'g');
            container.innerHTML = container.innerHTML.replace(
                regex,
                `<span class="car-solucion-highlight"><u>${textoBuscado}</u></span>`
            );
        });

        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>

</html>