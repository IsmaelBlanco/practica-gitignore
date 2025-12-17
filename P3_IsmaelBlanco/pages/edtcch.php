<?php
require_once "../php/conexion.php";

session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
if (!$isAdmin) {
    header('Location: /');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: /');
    exit;
}

$id = intval($_GET['id']);


$sql = "SELECT * FROM coches WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$coche = $stmt->fetch(PDO::FETCH_ASSOC);
$ganancia = $coche['precio_venta'] - $coche['precio_compra'] - $coche['precio_inversion'];

if (!$coche) {
    echo "Coche no encontrado.";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    $marca = $_POST['marca'] ?? '';
    $modelo = $_POST['modelo'] ?? '';
    $version = $_POST['version'] ?? '';
    $kms = floatval($_POST['kms'] ?? 0);
    $anio = intval($_POST['anio'] ?? 0);
    $combustible = $_POST['combustible'] ?? '';
    $caballos = intval($_POST['caballos'] ?? 0);
    $carroceria = $_POST['carroceria'] ?? '';
    $precio_compra = floatval($_POST['precio_compra'] ?? 0);
    $precio_inversion = floatval($_POST['precio_inversion'] ?? 0);
    $precio_venta = floatval($_POST['precio_venta'] ?? 0);
    $traccion = $_POST['traccion'] ?? '';
    $consumo = floatval($_POST['consumo'] ?? 0);
    $color = $_POST['color'] ?? '';

    $ganancia = $precio_venta - $precio_compra - $precio_inversion;

    $sql = "UPDATE coches SET marca = ?, modelo = ?, version = ?, kms = ?, anio = ?, combustible = ?, caballos = ?, carroceria = ?, precio_compra = ?, precio_inversion = ?, precio_venta = ?, ganancia = ?, traccion = ?, consumo = ?, color = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
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
        $color,
        $id
    ]);

    header("Location: /pages/edtcch?id=$id&updated=1");
    exit;
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
    <!-- Curiosamente no hay ningun problema reutilizando esto, me esperaba el banner volando o algo asi  -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js" defer></script>
    <title>Editar un coche | CarSolucion </title>
</head>

<body>
    <style>
        #foto {
            width: 60%;
            margin-top: 30%;
            margin-left: 20%;
            background-color: red;
            border-radius: 12px;
            padding: 1rem;

            p {
                margin: 0;
            }
        }
    </style>

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
            <a href="/pages/chgcch">Administrar catálogo</a>
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
            <a href="/pages/edtcch">Editar un coche</a>
        </div>
    </header>

    <main>
        <div class="formulario">
            <form id="formActualizaCoche" method="POST" action="edtcch.php?id=<?= $id ?>">
                <input type="hidden" name="id" id="idCoche" value="<?= $id ?>">
                <!-- Foto -->
                <div class="campo" id="foto">
                    <p>Las fotos no se pueden actualizar*</p>
                </div>

                <!-- Marca -->
                <div class="campo" id="marca">
                    <label for="marca">Marca:</label>
                    <input type="text" id="marca" name="marca" required
                        value="<?= htmlspecialchars($coche['marca']) ?>">
                </div>

                <!-- Modelo -->
                <div class="campo" id="modelo">
                    <label for="modelo">Modelo:</label>
                    <input type="text" id="modelo" name="modelo" required
                        value="<?= htmlspecialchars($coche['modelo']) ?>">
                </div>

                <!-- Versión -->
                <div class="campo" id="version">
                    <label for="version">Versión:</label>
                    <input type="text" id="version" name="version" value="<?= htmlspecialchars($coche['version']) ?>">
                </div>

                <!-- Kilómetros -->
                <div class="campo" id="kms">
                    <label for="kms">Kilómetros:</label>
                    <input type="number" id="kms" name="kms" step="0.1" min="0" required
                        value="<?= htmlspecialchars($coche['kms']) ?>">
                </div>

                <!-- Año -->
                <div class="campo" id="anio">
                    <label for="anio">Año:</label>
                    <input type="number" id="anio" name="anio" min="1800" max="2200" required
                        value="<?= htmlspecialchars($coche['anio']) ?>">
                </div>

                <!-- Combustible -->
                <div class="campo" id="combustible">
                    <label for="combustible">Combustible:</label>
                    <select id="combustible" name="combustible" required>
                        <option value="Diesel" <?= $coche['combustible'] === 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                        <option value="Gasolina" <?= $coche['combustible'] === 'Gasolina' ? 'selected' : '' ?>>Gasolina
                        </option>
                        <option value="Eléctrico" <?= $coche['combustible'] === 'Eléctrico' ? 'selected' : '' ?>>Eléctrico
                        </option>
                        <option value="Híbrido" <?= $coche['combustible'] === 'Híbrido' ? 'selected' : '' ?>>Híbrido
                        </option>
                    </select>
                </div>

                <!-- Caballos -->
                <div class="campo" id="caballos">
                    <label for="caballos">Caballos:</label>
                    <input type="number" id="caballos" name="caballos" min="0" required
                        value="<?= htmlspecialchars($coche['caballos']) ?>">
                </div>

                <!-- Carrocería -->
                <div class="campo" id="carroceria">
                    <label for="carroceria">Carrocería:</label>
                    <select id="carroceria" name="carroceria" required>
                        <option value="Sedán" <?= $coche['carroceria'] === 'Sedán' ? 'selected' : '' ?>>Sedán</option>
                        <option value="Hatchback" <?= $coche['carroceria'] === 'Hatchback' ? 'selected' : '' ?>>Hatchback
                        </option>
                        <option value="SUV" <?= $coche['carroceria'] === 'SUV' ? 'selected' : '' ?>>SUV</option>
                        <option value="Crossover" <?= $coche['carroceria'] === 'Crossover' ? 'selected' : '' ?>>Crossover
                        </option>
                        <option value="Coupé" <?= $coche['carroceria'] === 'Coupé' ? 'selected' : '' ?>>Coupé</option>
                        <option value="Pick-Up" <?= $coche['carroceria'] === 'Pick-Up' ? 'selected' : '' ?>>Pick-Up
                        </option>
                        <option value="Roadster" <?= $coche['carroceria'] === 'Roadster' ? 'selected' : '' ?>>Roadster
                        </option>
                        <option value="Convertible" <?= $coche['carroceria'] === 'Convertible' ? 'selected' : '' ?>>
                            Convertible</option>
                        <option value="Minivan" <?= $coche['carroceria'] === 'Minivan' ? 'selected' : '' ?>>Minivan
                        </option>
                        <option value="Familiar" <?= $coche['carroceria'] === 'Familiar' ? 'selected' : '' ?>>Familiar
                        </option>
                        <option value="Cabrio" <?= $coche['carroceria'] === 'Cabrio' ? 'selected' : '' ?>>Cabrio
                        </option>
                    </select>
                </div>

                <!-- Precio compra -->
                <div class="campo" id="precio_compradiv">
                    <label for="precio_compra">Precio compra (€):</label>
                    <input type="number" id="precio_compra" name="precio_compra" step="0.01" min="0" required
                        value="<?= htmlspecialchars($coche['precio_compra']) ?>">
                </div>

                <!-- Precio inversión -->
                <div class="campo" id="precio_inversiondiv">
                    <label for="precio_inversion">Precio inversión (€):</label>
                    <input type="number" id="precio_inversion" name="precio_inversion" step="0.01" min="0" required
                        value="<?= htmlspecialchars($coche['precio_inversion']) ?>">
                </div>

                <!-- Precio venta -->
                <div class="campo" id="precio_ventadiv">
                    <label for="precio_venta">Precio venta (€):</label>
                    <input type="number" id="precio_venta" name="precio_venta" step="0.01" min="0" required
                        value="<?= htmlspecialchars($coche['precio_venta']) ?>">
                </div>

                <!-- Ganancia (solo lectura) -->
                <div class="campo" id="gananciadiv">
                    <label for="ganancia">Ganancia (€):</label>
                    <input type="number" id="ganancia" name="ganancia" value="<?= htmlspecialchars($ganancia) ?>"
                        readonly>
                </div>

                <!-- Tracción -->
                <div class="campo" id="traccion">
                    <label for="traccion">Tracción:</label>
                    <select id="traccion" name="traccion" required>
                        <option value="Delantera" <?= $coche['traccion'] === 'Delantera' ? 'selected' : '' ?>>Delantera
                        </option>
                        <option value="Trasera" <?= $coche['traccion'] === 'Trasera' ? 'selected' : '' ?>>Trasera</option>
                        <option value="Mixta" <?= $coche['traccion'] === 'Mixta' ? 'selected' : '' ?>>Mixta</option>
                    </select>
                </div>

                <!-- Consumo -->
                <div class="campo" id="consumo">
                    <label for="consumo">Consumo (L/100km):</label>
                    <input type="number" id="consumo" name="consumo" step="0.1" min="0" required
                        value="<?= htmlspecialchars($coche['consumo']) ?>">
                </div>

                <!-- Color (checkboxes) -->
                <div class="campo" id="color">
                    <label>Color:</label>
                    <div class="color-options">
                        <label class="color-circle blanco">
                            <input type="radio" name="color" value="Blanco" <?= $coche['color'] === 'Blanco' ? 'checked' : '' ?>>
                            <span></span>
                        </label>
                        <label class="color-circle gris">
                            <input type="radio" name="color" value="Gris" <?= $coche['color'] === 'Gris' ? 'checked' : '' ?>>
                            <span></span>
                        </label>
                        <label class="color-circle negro">
                            <input type="radio" name="color" value="Negro" <?= $coche['color'] === 'Negro' ? 'checked' : '' ?>>
                            <span></span>
                        </label>
                        <label class="color-circle rojo">
                            <input type="radio" name="color" value="Rojo" <?= $coche['color'] === 'Rojo' ? 'checked' : '' ?>>
                            <span></span>
                        </label>
                        <label class="color-circle amarillo">
                            <input type="radio" name="color" value="Amarillo" <?= $coche['color'] === 'Amarillo' ? 'checked' : '' ?>>
                            <span></span>
                        </label>
                        <label class="color-circle verde">
                            <input type="radio" name="color" value="Verde" <?= $coche['color'] === 'Verde' ? 'checked' : '' ?>>
                            <span></span>
                        </label>
                        <label class="color-circle azul">
                            <input type="radio" name="color" value="Azul" <?= $coche['color'] === 'Azul' ? 'checked' : '' ?>>
                            <span></span>
                        </label>
                        <label class="color-circle naranja">
                            <input type="radio" name="color" value="Naranja" <?= $coche['color'] === 'Naranja' ? 'checked' : '' ?>>
                            <span></span>
                        </label>
                        <label class="color-circle rosa">
                            <input type="radio" name="color" value="Rosa" <?= $coche['color'] === 'Rosa' ? 'checked' : '' ?>>
                            <span></span>
                        </label>
                    </div>
                </div>
                <button type="submit">Actualizar coche</button>
            </form>
        </div>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="updateToast" class="toast align-items-center text-bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Coche actualizado con éxito
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Cerrar"></button>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('updated') === '1') {
                    const toastElement = document.getElementById('updateToast');
                    const toast = new bootstrap.Toast(toastElement);
                    toast.show();

                    urlParams.delete('updated');
                    const newUrl = window.location.pathname + '?' + urlParams.toString();
                    window.history.replaceState({}, document.title, newUrl);
                }
            });
            document.addEventListener('DOMContentLoaded', () => {
                const precioCompraInput = document.getElementById('precio_compra');
                const precioInversionInput = document.getElementById('precio_inversion');
                const precioVentaInput = document.getElementById('precio_venta');
                const gananciaInput = document.getElementById('ganancia');

                function actualizarGanancia() {
                    const compra = parseFloat(precioCompraInput.value) || 0;
                    const inversion = parseFloat(precioInversionInput.value) || 0;
                    const venta = parseFloat(precioVentaInput.value) || 0;
                    const ganancia = venta - compra - inversion;
                    gananciaInput.value = ganancia.toFixed(2);

                    if (ganancia >= 0) {
                        gananciaInput.style.color = "green";
                        gananciaInput.style.fontWeight = "bold";
                    } else {
                        gananciaInput.style.color = "red";
                        gananciaInput.style.fontWeight = "bold";
                    }
                }

                precioCompraInput.addEventListener('input', actualizarGanancia);
                precioInversionInput.addEventListener('input', actualizarGanancia);
                precioVentaInput.addEventListener('input', actualizarGanancia);

                actualizarGanancia();
            });
        </script>
</body>

</html>