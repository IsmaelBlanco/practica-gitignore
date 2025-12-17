<?php
require_once '../php/conexion.php';

session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

$where = [];
$params = [];

$where[] = "estado = ?";
$params[] = "Pendiente";

if (!empty($_GET['filtro_marcas'])) {
    $in = str_repeat('?,', count($_GET['filtro_marcas']) - 1) . '?';
    $where[] = "marca IN ($in)";
    $params = array_merge($params, $_GET['filtro_marcas']);
}

if (!empty($_GET['filtro_combustible'])) {
    $in = str_repeat('?,', count($_GET['filtro_combustible']) - 1) . '?';
    $where[] = "combustible IN ($in)";
    $params = array_merge($params, $_GET['filtro_combustible']);
}

if (isset($_GET['precio_min']) && is_numeric($_GET['precio_min'])) {
    $where[] = "precio_venta >= ?";
    $params[] = $_GET['precio_min'];
}

if (isset($_GET['precio_max']) && is_numeric($_GET['precio_max'])) {
    $where[] = "precio_venta <= ?";
    $params[] = $_GET['precio_max'];
}

if (!empty($_GET['filtro_carroceria'])) {
    $in = str_repeat('?,', count($_GET['filtro_carroceria']) - 1) . '?';
    $where[] = "carroceria IN ($in)";
    $params = array_merge($params, $_GET['filtro_carroceria']);
}

if (!empty($_GET['filtro_color'])) {
    $in = str_repeat('?,', count($_GET['filtro_color']) - 1) . '?';
    $where[] = "color IN ($in)";
    $params = array_merge($params, $_GET['filtro_color']);
}

if (isset($_GET['consumo_max']) && is_numeric($_GET['consumo_max'])) {
    $where[] = "consumo <= ?";
    $params[] = $_GET['consumo_max'];
}

$por_pagina = 18;
$pagina = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
$offset = ($pagina - 1) * $por_pagina;

$sql_count = "SELECT COUNT(*) FROM coches";
if ($where) {
    $sql_count .= " WHERE " . implode(' AND ', $where);
}
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute($params);
$total_resultados = $stmt_count->fetchColumn();
$total_paginas = ceil($total_resultados / $por_pagina);

$sql = "SELECT * FROM coches";
if ($where) {
    $sql .= " WHERE " . implode(' AND ', $where);
}
$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$params_paginados = array_merge($params, [$por_pagina, $offset]);

$stmt = $pdo->prepare($sql);
$stmt->execute($params_paginados);

$marcas = $pdo->query("SELECT DISTINCT marca FROM coches WHERE estado = 'Pendiente' ORDER BY marca")->fetchAll(PDO::FETCH_COLUMN);
$combustibles = $pdo->query("SELECT DISTINCT combustible FROM coches WHERE estado = 'Pendiente' ORDER BY combustible")->fetchAll(PDO::FETCH_COLUMN);
$carrocerias = $pdo->query("SELECT DISTINCT carroceria FROM coches WHERE estado = 'Pendiente' ORDER BY carroceria")->fetchAll(PDO::FETCH_COLUMN);
$maxPrecio = $pdo->query("SELECT MAX(precio_venta) FROM coches WHERE estado = 'Pendiente'")->fetchColumn();
$minPrecio = $pdo->query("SELECT MIN(precio_venta) FROM coches WHERE estado = 'Pendiente'")->fetchColumn();



function precioToPercent($precio, $min, $max)
{
    if ($max == $min)
        return 0;
    return (($precio - $min) * 100) / ($max - $min);
}

$minValuePercent = precioToPercent($minPrecio, $minPrecio, $maxPrecio);
$maxValuePercent = precioToPercent($maxPrecio, $minPrecio, $maxPrecio);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="icon" href="/src/logo.png" type="image/png">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/catalogostyle.css">
    <link href="https://cdn.jsdelivr.net/npm/open-dyslexic@1.0.0/open-dyslexic.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Solo lo uso para el boton de movil -->
    <script src="/js/accessibility.js" defer></script>
    <script src="../js/filtmodelo.js" defer></script>
    <script src="../js/sliderprecio.js" defer></script>
    <script src="../js/bootstrap.bundle.min.js" defer></script>
    <script src="../js/fliparrow.js" defer></script>
    <title>Catálogo | CarSolucion </title>
</head>

<body>
    <!-- Botón flotante para móvil -->
    <button class="btn-filter-toggle" id="toggleAside">
        <i class="fas fa-filter"></i>
    </button>

    <!-- Overlay para cerrar filtros -->
    <div class="filter-overlay" id="filterOverlay"></div>
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
        </div>
    </header>

    <main>
        <aside id="filtrosAside">
            <p class="flts">Filtros</p>
            <form method="GET" action="catalogo.php">
                <button href="#" class="button" type="submit" style="--clr:rgb(210, 0, 0)">
                    <span class="button__icon-wrapper">
                        <!-- 
                            Icono extraído de: https://uiverse.io/Creatlydev/silly-cat-86
                            Autor: Samir Yangua Ruiz
                            Licencia: MIT License

                            Copyright (c) 2025 Samir Yangua Ruiz
                            Permiso concedido, de forma gratuita, para usar, copiar, modificar, fusionar, 
                            publicar, distribuir, sublicenciar y/o vender copias del Software...
                            -->
                        <svg viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="button__icon-svg"
                            width="10">
                            <path
                                d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                                fill="currentColor"></path>
                        </svg>

                        <svg viewBox="0 0 14 15" fill="none" width="10" xmlns="http://www.w3.org/2000/svg"
                            class="button__icon-svg button__icon-svg--copy">
                            <path
                                d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"
                                fill="currentColor"></path>
                        </svg>
                    </span>
                    Filtrar
                </button>
                <hr>
                <button class="btnfiltrado" type="button" data-bs-toggle="collapse" data-bs-target="#listaMarcas"
                    aria-expanded="false" aria-controls="listaMarcas">
                    <h3 class="titulo-filtro">
                        Marca
                    </h3>
                    <!-- Icono “chevron-down” de Feather Icons (MIT License) -->
                    <!-- Copyright (c) Feather Icons contributors -->
                    <svg class="flecha" xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>

                </button>
                <div class="collapse mt-2" id="listaMarcas">
                    <div class="card card-body">
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($marcas as $marca): ?>
                                <li>
                                    <label>
                                        <input type="checkbox" name="filtro_marcas[]"
                                            value="<?= htmlspecialchars($marca) ?>">
                                        <?= htmlspecialchars($marca) ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <hr>
                <button id="btnModelos" class="btnfiltrado" type="button" data-bs-toggle="collapse"
                    data-bs-target="#listaModelos" aria-expanded="false" aria-controls="listaModelos">
                    <h3 class="titulo-filtro">
                        Modelo
                    </h3>
                    <svg class="flecha" xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>

                </button>
                <div class="collapse mt-2" id="listaModelos">
                    <div class="card card-body">
                        <div id="modeloMensaje" style="display:none;">Selecciona una marca</div>
                        <ul class="list-unstyled mb-0" id="listaModelosUl">
                            <!-- modelos vía JS -->
                        </ul>
                    </div>
                </div>

                <hr>
                <button class="btnfiltrado" type="button" data-bs-toggle="collapse" data-bs-target="#porPrecio"
                    aria-expanded="false" aria-controls="porPrecio">
                    <h3 class="titulo-filtro">
                        Precio
                    </h3>
                    <svg class="flecha" xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>

                </button>
                <div class="collapse mt-2" id="porPrecio">
                    <div class="card card-body">
                        <div class="double_range_slider_box">
                            <div class="double_range_slider">
                                <span class="range_track" id="range_track"></span>

                                <input type="range" class="min" min="0" max="100" value="<?= $minValuePercent ?>"
                                    data-min="<?= $minPrecio ?>" data-max="<?= $maxPrecio ?>">
                                <input type="range" class="max" min="0" max="100" value="<?= $maxValuePercent ?>"
                                    data-min="<?= $minPrecio ?>" data-max="<?= $maxPrecio ?>">

                                <!-- Inputs ocultos que se envían al backend -->
                                <input type="hidden" name="precio_min" id="precioMinInput" value="<?= $minPrecio ?>">
                                <input type="hidden" name="precio_max" id="precioMaxInput" value="<?= $maxPrecio ?>">

                                <div class="labelValues">
                                    <div class="minvalue"></div>
                                    <div class="maxvalue"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <button class="btnfiltrado" type="button" data-bs-toggle="collapse" data-bs-target="#listaCombustible"
                    aria-expanded="false" aria-controls="listaCombustible">
                    <h3 class="titulo-filtro">
                        Combustible
                    </h3>
                    <svg class="flecha" xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>

                </button>
                <div class="collapse mt-2" id="listaCombustible">
                    <div class="card card-body">
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($combustibles as $comb): ?>
                                <li>
                                    <label>
                                        <input type="checkbox" name="filtro_combustible[]"
                                            value="<?= htmlspecialchars($comb) ?>">
                                        <?= htmlspecialchars($comb) ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <!-- Filtro por carrocería -->
                <hr>
                <button class="btnfiltrado" type="button" data-bs-toggle="collapse" data-bs-target="#listaCarroceria"
                    aria-expanded="false">
                    <h3 class="titulo-filtro">Carrocería</h3>
                    <svg class="flecha" xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="collapse mt-2" id="listaCarroceria">
                    <div class="card card-body">
                        <ul class="list-unstyled mb-0">
                            <?php
                            foreach ($carrocerias as $c):
                                ?>
                                <li>
                                    <label><input type="checkbox" name="filtro_carroceria[]"
                                            value="<?= htmlspecialchars($c) ?>"> <?= htmlspecialchars($c) ?></label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Filtro por color -->
                <hr>
                <button class="btnfiltrado" type="button" data-bs-toggle="collapse" data-bs-target="#listaColor"
                    aria-expanded="false">
                    <h3 class="titulo-filtro">Color</h3>
                    <svg class="flecha" xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="collapse mt-2" id="listaColor">
                    <div class="card card-body">
                        <ul class="list-unstyled mb-0 d-flex flex-wrap gap-2">
                            <?php
                            $coloresHex = [
                                'Rojo' => '#ff0000',
                                'Azul' => '#0000ff',
                                'Negro' => '#000000',
                                'Verde' => '#00ff00',
                                'Amarillo' => '#ffff00',
                                'Naranja' => '#ffa500',
                                'Rosa' => '#ffc0cb',
                                'Gris' => '#808080',
                                'Blanco' => '#ffffff',
                            ];

                            foreach ($coloresHex as $nombre => $hex):
                                ?>
                                <li>
                                    <input type="checkbox" name="filtro_color[]" value="<?= $nombre ?>"
                                        id="color-<?= $nombre ?>" hidden>
                                    <label for="color-<?= $nombre ?>" class="color-circle"
                                        style="background-color: <?= $hex ?>;" title="<?= $nombre ?>"></label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Filtro por consumo máximo -->
                <hr>
                <button class="btnfiltrado" type="button" data-bs-toggle="collapse" data-bs-target="#filtroConsumo"
                    aria-expanded="false">
                    <h3 class="titulo-filtro">Consumo</h3>
                    <svg class="flecha" xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="collapse mt-2 titulo-filtro" id="filtroConsumo">
                    <div class="card card-body">
                        <input type="number" name="consumo_max" min="0" class="form-control"
                            placeholder="Ej: 7 (L/100km)">
                    </div>
                </div>
            </form>

        </aside>

        <div class="maincontent">
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <div class="publicaciones">
                    <img src="/src/uploads/principal_<?= htmlspecialchars($row['id']) ?>.jpg">
                    <div class="pubtext">
                        <p><strong><?= htmlspecialchars($row['marca']) ?>     <?= htmlspecialchars($row['modelo']) ?></strong>
                        </p>
                    </div>
                    <p class="precio"><?= number_format($row['precio_venta'], 2) ?>€</p>
                    <div class="tags">
                        <div class="tag"><?= htmlspecialchars($row['combustible']) ?></div>
                        <div class="tag"><?= htmlspecialchars($row['caballos']) ?> CV</div>
                        <?php if ($row['combustible'] != "Eléctrico"): ?>
                            <div class="tag"><?= htmlspecialchars($row['consumo']) ?>L/100km</div>
                        <?php else: ?>
                            <div class="tag"><?= htmlspecialchars($row['consumo']) ?>kWh/100km</div>
                        <?php endif; ?>

                        <div class="custom-button" data-id="<?= htmlspecialchars($row['id']) ?>">
                            <div class="button-wrapper">
                                <div class="text">Detalles</div>
                                <span class="icon">
                                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg"
                                        xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 324.018 324.017"
                                        xml:space="preserve" width="40" height="40" fill="currentColor">
                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                        <g id="SVGRepo_iconCarrier">
                                            <g>
                                                <g>
                                                    <path
                                                        d="M317.833,197.111c3.346-11.148,2.455-20.541-2.65-27.945c-9.715-14.064-31.308-15.864-35.43-16.076l-8.077-4.352 l-0.528-0.217c-8.969-2.561-42.745-3.591-47.805-3.733c-7.979-3.936-14.607-7.62-20.475-10.879 c-20.536-11.413-34.107-18.958-72.959-18.958c-47.049,0-85.447,20.395-90.597,23.25c-2.812,0.212-5.297,0.404-7.646,0.59 l-6.455-8.733l7.34,0.774c2.91,0.306,4.267-1.243,3.031-3.459c-1.24-2.216-4.603-4.262-7.519-4.57l-23.951-2.524 c-2.91-0.305-4.267,1.243-3.026,3.459c1.24,2.216,4.603,4.262,7.519,4.57l3.679,0.386l8.166,11.05 c-13.823,1.315-13.823,2.139-13.823,4.371c0,18.331-2.343,22.556-2.832,23.369L0,164.443v19.019l2.248,2.89 c-0.088,2.775,0.823,5.323,2.674,7.431c5.981,6.804,19.713,7.001,21.256,7.001c4.634,0,14.211-2.366,20.78-4.153 c-0.456-0.781-0.927-1.553-1.3-2.392c-0.36-0.809-0.603-1.668-0.885-2.517c-0.811-2.485-1.362-5.096-1.362-7.845 c0-14.074,11.449-25.516,25.515-25.516s25.52,11.446,25.52,25.521c0,6.068-2.221,11.578-5.773,15.964 c-0.753,0.927-1.527,1.828-2.397,2.641c-1.022,0.958-2.089,1.859-3.254,2.641c29.332,0.109,112.164,0.514,168.708,1.771 c-0.828-0.823-1.533-1.771-2.237-2.703c-0.652-0.854-1.222-1.75-1.761-2.688c-2.164-3.744-3.5-8.025-3.5-12.655 c0-14.069,11.454-25.513,25.518-25.513c14.064,0,25.518,11.449,25.518,25.513c0,5.126-1.553,9.875-4.152,13.878 c-0.605,0.922-1.326,1.755-2.04,2.594c-0.782,0.922-1.616,1.781-2.527,2.584c5.209,0.155,9.699,0.232,13.546,0.232 c19.563,0,23.385-1.688,23.861-5.018C324.114,202.108,324.472,199.602,317.833,197.111z">
                                                    </path>
                                                    <path
                                                        d="M52.17,195.175c3.638,5.379,9.794,8.922,16.756,8.922c0.228,0,0.44-0.062,0.663-0.073c2.576-0.083,5.043-0.61,7.291-1.574 c1.574-0.678,2.996-1.6,4.332-2.636c4.782-3.702,7.927-9.429,7.927-15.933c0-11.144-9.066-20.216-20.212-20.216 s-20.213,9.072-20.213,20.216c0,2.263,0.461,4.411,1.149,6.446c0.288,0.85,0.616,1.673,1.015,2.471 C51.279,193.606,51.667,194.434,52.17,195.175z">
                                                    </path>
                                                    <path
                                                        d="M269.755,209.068c2.656,0,5.173-0.549,7.503-1.481c1.589-0.642,3.06-1.491,4.422-2.495 c1.035-0.767,1.988-1.616,2.863-2.559c3.34-3.604,5.432-8.389,5.432-13.681c0-11.144-9.071-20.21-20.215-20.21 s-20.216,9.066-20.216,20.21c0,4.878,1.812,9.3,4.702,12.801c0.818,0.989,1.719,1.89,2.708,2.713 c1.311,1.088,2.729,2.024,4.293,2.755C263.836,208.333,266.704,209.068,269.755,209.068z">
                                                    </path>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            <?php } ?>
            <?php
            $filtros = $_GET;
            unset($filtros['pagina']);
            $base_query = http_build_query($filtros);

            $start = max(1, $pagina - 2);
            $end = min($total_paginas, $pagina + 2);
            ?>
            <div class="pagination">
                <ul>
                    <?php if ($pagina > 1): ?>
                        <li class="prev">
                            <a href="?<?= $base_query ?>&pagina=<?= $pagina - 1 ?>">Anterior</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="<?= $i === $pagina ? 'active' : '' ?>">
                            <a href="?<?= $base_query ?>&pagina=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagina < $total_paginas): ?>
                        <li class="next">
                            <a href="?<?= $base_query ?>&pagina=<?= $pagina + 1 ?>">Siguiente</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleAside = document.getElementById('toggleAside');
            const aside = document.getElementById('filtrosAside');
            const filterOverlay = document.getElementById('filterOverlay');
            const mainContent = document.querySelector('.maincontent');

            function toggleMenu() {
                aside.classList.toggle('visible');
                filterOverlay.classList.toggle('visible');
                document.body.classList.toggle('no-scroll');
            }

            // Toggle del aside
            toggleAside.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleMenu();
            });

            // Cerrar al hacer clic en el overlay
            filterOverlay.addEventListener('click', toggleMenu);

            // Cerrar al hacer clic en el botón de filtrar (en móvil)
            const filterButton = document.querySelector('.button[type="submit"]');
            if (filterButton) {
                filterButton.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        toggleMenu();
                    }
                });
            }

            // Manejar cambios de tamaño
            function handleResize() {
                if (window.innerWidth >= 992) {
                    aside.classList.remove('visible');
                    filterOverlay.classList.remove('visible');
                    document.body.classList.remove('no-scroll');
                }
            }

            window.addEventListener('resize', handleResize);
        });
    </script>
</body>

</html>