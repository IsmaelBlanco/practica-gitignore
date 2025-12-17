<?php
session_start();
require_once "../php/conexion.php";
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!$isAdmin) {
    header("Location: /");
    exit;
}

// Función para limpiar datos, evito vulnerabilidad XSS y limpio formato
function clean($data)
{
    return htmlspecialchars(trim($data));
}

// Función para convertir imágenes a JPG (soporta jpeg, png, webp)
function convertirAJPG($origen, $destino)
{
    $info = getimagesize($origen);
    if (!$info)
        return false;

    switch ($info['mime']) {
        case 'image/jpeg':
            $img = imagecreatefromjpeg($origen);
            break;
        case 'image/png':
            $img = imagecreatefrompng($origen);
            break;
        case 'image/webp':
            $img = imagecreatefromwebp($origen);
            break;
        default:
            return false; // formato no soportado
    }
    if (!$img)
        return false;
    $res = imagejpeg($img, $destino, 90);
    imagedestroy($img);
    return $res;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $marca = clean($_POST['marca']);
    $modelo = clean($_POST['modelo']);
    $version = clean($_POST['version']);
    $kms = floatval($_POST['kms']);
    $anio = intval($_POST['anio']);
    $combustible = clean($_POST['combustible']);
    $caballos = intval($_POST['caballos']);
    $carroceria = clean($_POST['carroceria']);
    $precio_compra = floatval($_POST['precio_compra']);
    $precio_inversion = floatval($_POST['precio_inversion']);
    $precio_venta = floatval($_POST['precio_venta']);
    $ganancia = $precio_venta - ($precio_compra + $precio_inversion);
    $traccion = clean($_POST['traccion']);
    $consumo = floatval($_POST['consumo']);
    $color = isset($_POST['color']) ? clean($_POST['color']) : null;

    $stmt = $pdo->prepare("INSERT INTO coches 
        (marca, modelo, version, kms, anio, combustible, caballos, carroceria, precio_compra, precio_inversion, precio_venta, ganancia, traccion, consumo, color) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $resultado = $stmt->execute([
        $marca,
        $modelo,
        $version,
        $kms,
        $anio,
        $combustible,
        $caballos,
        $carroceria,
        $precio_compra,
        $precio_inversion,
        $precio_venta,
        $ganancia,
        $traccion,
        $consumo,
        $color
    ]);

    if ($resultado) {
        $last_id = $pdo->lastInsertId();

        $uploadDir = "../src/uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Ruta relativa para imagen principal (la primera)
        $foto_principal_rel = "principal_{$last_id}.jpg";
        var_dump($_FILES['foto']);
        foreach ($_FILES['foto']['tmp_name'] as $index => $tmpName) {
            if (is_uploaded_file($tmpName)) {
                // Nombre del archivo
                if ($index === 0) {
                    $filename = "principal_{$last_id}.jpg";
                } else {
                    $filename = "{$index}_{$last_id}.jpg";
                }

                $destino = $uploadDir . $filename;

                // Intentar convertir a JPG
                $info = getimagesize($tmpName);

                if ($info && in_array($info['mime'], ['image/jpeg', 'image/png', 'image/webp'])) {
                    // Si es una imagen válida, intenta convertirla a JPG
                    if (!convertirAJPG($tmpName, $destino)) {
                        // Si falla la conversión, aún así intenta moverla
                        move_uploaded_file($tmpName, $destino);
                    }

                    // Guardar ruta en DB como antes...
                    if ($index === 0) {
                        $foto_principal_rel = $filename;
                        $stmt2 = $pdo->prepare("UPDATE coches SET foto = ? WHERE id = ?");
                        $stmt2->execute([$foto_principal_rel, $last_id]);
                    } else {
                        $stmtImg = $pdo->prepare("INSERT INTO coche_imagenes (coche_id, ruta_imagen) VALUES (?, ?)");
                        $stmtImg->execute([$last_id, $filename]);
                    }

                } else {
                    echo "Archivo no permitido: " . $_FILES['foto']['name'][$index] . "<br>";
                    continue;
                }
            }
        }

        // Guardar la ruta de la imagen principal en la DB
        $stmt2 = $pdo->prepare("UPDATE coches SET foto = ? WHERE id = ?");
        $stmt2->execute([$foto_principal_rel, $last_id]);

        header("Location: /pages/admin-area?success=1");
        exit;
    } else {
        echo "Error al insertar: ";
        print_r($stmt->errorInfo());
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/src/logo.png" type="image/png">   
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/addcchstyle.css">
    <script src="../js/bootstrap.bundle.min.js" defer></script>
    <script src="../js/addcch.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <title>Subir un coche | CarSolucion </title>
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
            <a href="/pages/addcch">Subir un coche</a>
        </div>
    </header>

    <main>
        <div class="formulario">
            <form method="POST" action="addcch.php" enctype="multipart/form-data">

                <!-- Foto -->
                <div class="campo" id="foto">
                    <label for="fotoDrop">Fotos (arrastrar o hacer clic):</label>
                    <div id="dropArea" class="drop-area">
                        <p>Arrastra las imágenes aquí o haz clic para seleccionarlas (máximo 9)</p>
                        <input type="file" id="fotoDrop" name="foto[]" accept="image/*" multiple hidden>
                    </div>
                    <div id="vistaFotos" class="sortable-images"></div>
                    <button type="button" id="vaciarFotos" class="btn-rojo">Vaciar imágenes</button>
                </div>

                <!-- Marca -->
                <div class="campo" id="marca">
                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca" required>
                </div>

                <!-- Modelo -->
                <div class="campo" id="modelo">
                    <label for="modelo">Modelo:</label>
                    <input type="text" id="modelo" name="modelo" required>
                </div>

                <!-- Versión -->
                <div class="campo" id="version">
                    <label for="version">Versión:</label>
                    <input type="text" id="version" name="version">
                </div>

                <!-- Kilómetros -->
                <div class="campo" id="kms">
                    <label for="kms">Kilómetros:</label>
                    <input type="number" id="kms" name="kms" step="0.1" min="0" required>
                </div>

                <!-- Año -->
                <div class="campo" id="anio">
                    <label for="anio">Año:</label>
                    <input type="number" id="anio" name="anio" min="1800" max="2200" required>
                </div>

                <!-- Combustible -->
                <div class="campo" id="combustible">
                    <label for="combustible">Combustible:</label>
                    <select id="combustible" name="combustible" required>
                        <option value="Diesel">Diesel</option>
                        <option value="Gasolina">Gasolina</option>
                        <option value="Eléctrico">Eléctrico</option>
                        <option value="Híbrido">Híbrido</option>
                    </select>
                </div>

                <!-- Caballos -->
                <div class="campo" id="caballos">
                    <label for="caballos">Caballos:</label>
                    <input type="number" id="caballos" name="caballos" min="0" required>
                </div>

                <!-- Carrocería -->
                <div class="campo" id="carroceria">
                    <label for="carroceria">Carrocería:</label>
                    <select id="carroceria" name="carroceria" required>
                        <option value="Sedán">Sedán</option>
                        <option value="Hatchback">Hatchback</option>
                        <option value="SUV">SUV</option>
                        <option value="Crossover">Crossover</option>
                        <option value="Coupé">Coupé</option>
                        <option value="Pick-Up">Pick-Up</option>
                        <option value="Roadster">Roadster</option>
                        <option value="Convertible">Convertible</option>
                        <option value="Minivan">Minivan</option>
                        <option value="Familiar">Familiar</option>
                        <option value="Cabrio">Cabrio</option>
                    </select>
                </div>

                <!-- Precio compra -->
                <div class="campo" id="precio_compradiv">
                    <label for="precio_compra">Precio compra (€):</label>
                    <input type="number" id="precio_compra" name="precio_compra" step="0.01" min="0" required>
                </div>

                <!-- Precio inversión -->
                <div class="campo" id="precio_inversiondiv">
                    <label for="precio_inversion">Precio inversión (€):</label>
                    <input type="number" id="precio_inversion" name="precio_inversion" step="0.01" min="0" required>
                </div>

                <!-- Precio venta -->
                <div class="campo" id="precio_ventadiv">
                    <label for="precio_venta">Precio venta (€):</label>
                    <input type="number" id="precio_venta" name="precio_venta" step="0.01" min="0" required>
                </div>

                <!-- Ganancia (solo lectura) -->
                <div class="campo" id="gananciadiv">
                    <label for="ganancia">Ganancia (€):</label>
                    <input type="number" id="ganancia" name="ganancia" value="" placeholder="" readonly>
                </div>

                <!-- Tracción -->
                <div class="campo" id="traccion">
                    <label for="traccion">Tracción:</label>
                    <select id="traccion" name="traccion" required>
                        <option value="Delantera">Delantera</option>
                        <option value="Trasera">Trasera</option>
                        <option value="Mixta">Mixta</option>
                    </select>
                </div>

                <!-- Consumo -->
                <div class="campo" id="consumo">
                    <label for="consumo">Consumo (L/100km):</label>
                    <input type="number" id="consumo" name="consumo" step="0.1" min="0" required>
                </div>

                <!-- Color (checkboxes) -->
                <div class="campo" id="color">
                    <label>Color:</label>
                    <div class="color-options">
                        <label class="color-circle blanco">
                            <input type="radio" name="color" value="Blanco">
                            <span></span>
                        </label>
                        <label class="color-circle gris">
                            <input type="radio" name="color" value="Gris">
                            <span></span>
                        </label>
                        <label class="color-circle negro">
                            <input type="radio" name="color" value="Negro">
                            <span></span>
                        </label>
                        <label class="color-circle rojo">
                            <input type="radio" name="color" value="Rojo">
                            <span></span>
                        </label>
                        <label class="color-circle amarillo">
                            <input type="radio" name="color" value="Amarillo">
                            <span></span>
                        </label>
                        <label class="color-circle verde">
                            <input type="radio" name="color" value="Verde">
                            <span></span>
                        </label>
                        <label class="color-circle azul">
                            <input type="radio" name="color" value="Azul">
                            <span></span>
                        </label>
                        <label class="color-circle naranja">
                            <input type="radio" name="color" value="Naranja">
                            <span></span>
                        </label>
                        <label class="color-circle rosa">
                            <input type="radio" name="color" value="Rosa">
                            <span></span>
                        </label>
                    </div>
                </div>

                <button type="submit">Agregar coche</button>
            </form>
        </div>
    </main>
</body>

</html>