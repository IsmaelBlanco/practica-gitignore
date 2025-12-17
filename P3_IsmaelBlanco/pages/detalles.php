<?php
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
require_once "../php/conexion.php";

function limpiarTildes($texto)
{
    $texto = strtolower($texto);
    $texto = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ', ' '],
        ['a', 'e', 'i', 'o', 'u', 'n', '_'],
        $texto
    );
    return $texto;
}


if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmtch = $pdo->prepare("SELECT * FROM coches WHERE id = ?");
    $stmtch->execute([$id]);
    $coche = $stmtch->fetch();

    $stmtft = $pdo->prepare("SELECT * FROM coche_imagenes WHERE coche_id = ?");
    $stmtft->execute([$id]);
    $fotos = $stmtft->fetchAll();

    if (!$coche) {
        header("Location: /");
        exit;
    }
} else {
    header("Location: /");
    exit;
}


// Mapeo de nombres a colores CSS
$colores = [
    'Rojo' => '#ff0000',
    'Blanco' => '#ffffff',
    'Negro' => '#000000',
    'Azul' => '#0000ff',
    'Verde' => '#00ff00',
    'Gris' => '#cccccc',
    'Rosa' => '#ffc0cb',
    'Amarillo' => '#ffff00',
    'Naranja' => '#ffa500',
];

$nombreColor = htmlspecialchars($coche['color']);
$colorHex = $colores[$nombreColor] ?? '#cccccc';    //Si no está definido, gris.

//Mensaje WhatsApp
$telefono = '34611365892';
$mensaje = "Hola, quiero más información sobre el {$coche['marca']} {$coche['modelo']} {$coche['version']}";
$mensajeCodificado = urlencode($mensaje);

$whatsappLink = "https://api.whatsapp.com/send?phone={$telefono}&text={$mensajeCodificado}";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/src/logo.png" type="image/png">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/detallesstyle.css">
    <link href="https://cdn.jsdelivr.net/npm/open-dyslexic@1.0.0/open-dyslexic.css" rel="stylesheet">
    <script src="/js/accessibility.js" defer></script>
    <script src="/js/zoom.js" defer></script>
    <script src="/js/bootstrap.bundle.min.js" defer></script>
    <title>Detalles del <?= $coche['marca'] . " " . $coche['modelo'] . " " . $coche['version'] ?> | CarSolucion </title>
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
            <a href="/pages/catalogo">Vehículos</a>
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
            <a href="/pages/detalles.php?id=<?= htmlspecialchars($_GET['id']) ?>">Detalles del
                <?= htmlspecialchars($coche['marca']) . " " . htmlspecialchars($coche['modelo']) . " " . htmlspecialchars($coche['version']) ?></a>
        </div>
    </header>

    <main>
        <div class="content">
            <div id="contimg">
                <img src="/src/uploads/<?= htmlspecialchars($coche['foto']) ?>">
            </div>
            <div id="contfotos">
                <?php
                $totalFotos = count($fotos);
                if ($totalFotos > 1):
                    for ($i = 0; $i < $totalFotos - 1; $i++):
                        $foto = $fotos[$i];
                        ?>
                        <div class="eachfoto">
                            <img src="/src/uploads/<?= htmlspecialchars($foto['ruta_imagen']) ?>">
                        </div>
                    <?php endfor; ?>
                <?php elseif ($totalFotos == 1): ?>
                    <p>No hay más fotos disponibles</p>
                <?php endif; ?>
            </div>
            <div id="contprecio">
                <p class="ac">Precio al contado:</p>
                <p><?= htmlspecialchars($coche['precio_venta']) ?>€</p>
                <a class="btn whatsapp-button" href="<?= $whatsappLink ?>" target="_blank" rel="noopener noreferrer">
                    Preguntar por este coche
                    <img src="/src/whatsapp-icon.png" alt="WhatsApp" />
                </a>
            </div>
            <div id="contalldet">
                <div id="contfotdet">
                    <?php if (!empty($fotos)): ?>
                        <?php
                        $ultimaFoto = end($fotos);
                        reset($fotos);
                        ?>
                        <img src="/src/uploads/<?= htmlspecialchars($ultimaFoto['ruta_imagen']) ?>">
                    <?php endif; ?>
                </div>
                <div id="contdetalles">
                    <h2><strong><?= htmlspecialchars($coche['marca']) . " " . htmlspecialchars($coche['modelo']) . " " . htmlspecialchars($coche['version']) ?></strong>
                    </h2>
                    <img class="typec" src="/src/types/<?= limpiarTildes($coche['carroceria']) ?>.png">
                    <div class="detalle-grid">
                        <div class="detalle-item">
                            <span class="detalle-label">Kilometraje</span>
                            <span class="detalle-valor"><?= htmlspecialchars($coche['kms']) ?>kms<img src="/src/kms.png"></span>
                        </div>
                        <div class="detalle-item">
                            <span class="detalle-label">Año</span>
                            <span class="detalle-valor"><?= htmlspecialchars($coche['anio']) ?><img src="/src/anio.png"></span>
                        </div>
                        <div class="detalle-item">
                            <span class="detalle-label">Combustible</span>
                            <span class="detalle-valor"><?= htmlspecialchars($coche['combustible']) ?><img src="/src/comb.png"></span>
                        </div>
                        <div class="detalle-item">
                            <span class="detalle-label">Potencia</span>
                            <span class="detalle-valor"><?= htmlspecialchars($coche['caballos']) ?> CV<img src="/src/cvs.png"></span>
                        </div>
                        <div class="detalle-item">
                            <span class="detalle-label">Carrocería</span>
                            <span class="detalle-valor"><?= htmlspecialchars($coche['carroceria']) ?><img src="/src/carrc.png"></span>
                        </div>
                        <div class="detalle-item">
                            <span class="detalle-label">Tracción</span>
                            <span class="detalle-valor"><?= htmlspecialchars($coche['traccion']) ?><img src="/src/trcc.png"></span>
                        </div>
                        <div class="detalle-item">
                            <span class="detalle-label">Consumo</span>
                            <span class="detalle-valor"><?= htmlspecialchars($coche['consumo']) ?>
                                <?php
                                if (in_array($coche['combustible'], ['Diesel', 'Gasolina', 'Hibrido'])) {
                                    echo ' L/100km';
                                } elseif ($coche['combustible'] === 'Electrico') {
                                    echo ' kW/100km';
                                }
                                ?>
                            <img src="/src/cnsm.png">
                            </span>
                        </div>
                        <div class="detalle-item">
                            <span class="detalle-label">Color</span>
                            <span class="detalle-valor">
                                <?= $nombreColor ?>
                                <div
                                    style="width: 18px; height: 18px; border-radius: 50%; background-color: <?= $colorHex ?>; border: 1px solid #000;">
                                </div>
                                
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($isAdmin): ?>
                <div id="Admininfo">
                    <h1>Información para administadores:</h1>
                    <ul>
                        <li>
                            <h4>
                                <pre>Precio de compra:&#9;<?= htmlspecialchars($coche['precio_compra']) ?></pre>
                            </h4>
                        </li>
                        <li>
                            <h4>
                                <pre>Inversión:&#9;&#9;<?= htmlspecialchars($coche['precio_inversion']) ?></h4></li></li>
                            <li><h4><pre>Ganancia:&#9;&#9;<?= htmlspecialchars($coche['ganancia']) ?><pre></h4></li></li>
                        </ul>
                    
                    </div>
            <?php endif; ?>
        </div>
    </main>
    <footer class="footer">
        &copy; <span id="year"></span> CarSolucion. Todos los derechos reservados.
    </footer>
    <div id="zoom-container">
        <div id="zoom-content">
            <img id="zoom-image" src="" alt="Zoomed">
            <div id="zoom-close">&times;</div>
        </div>
    </div>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>

</html>