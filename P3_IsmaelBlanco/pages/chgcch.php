<?php
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
require_once "../php/conexion.php";

if (!$isAdmin) {
    header('Location: /');
    exit;
}

$sql = "SELECT * FROM coches where estado='Pendiente'";
$coches = $pdo->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/src/logo.png" type="image/png">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/chgcchstyle.css">
    <script src="../js/chgcchemergentes.js" defer></script>
    <script src="../js/bootstrap.bundle.min.js" defer></script>
    <title>Administrar catálogo | CarSolucion </title>
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
            <a href="/pages/chgcch">Administrar catálogo</a>
        </div>
    </header>

    <main>
        <?php foreach ($coches as $coche): ?>
            <div class="coche">
                <img id="cchimg" src="../src/uploads/<?= $coche['foto'] ?>">
                <div id="nomb">
                    <p><?= $coche['marca'] . " " . $coche['modelo'] . " " . $coche['version'] ?></p>
                </div>
                <div id="data">
                    <p><?= $coche['kms'] . "kms | " . $coche['combustible'] . " | " . $coche['caballos'] . "CVS | " . $coche['color'] . " | " . $coche['precio_venta'] . "€" ?>
                    </p>
                </div>
                <div id="botones">
                    <a href="edtcch.php?id=<?= $coche['id'] ?>" class="btn">Editar</a>
                    <button class="btn eliminar-btn" data-id="<?= $coche['id'] ?>">Eliminar</button>
                </div>
            </div>
        <?php endforeach; ?>
    </main>
    <!-- Modal de confirmación 
        (Me cansé de escribir el css a mano) -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p>¿Por qué quieres eliminar este coche?</p>
                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <button id="motivoVendido" class="btn btn-success">Vendido</button>
                        <button id="motivoEliminado" class="btn btn-danger">Eliminado</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Toast de confirmación -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div id="estadoToast" class="toast align-items-center text-white bg-success border-0" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="estadoToastMsg">
                    Coche actualizado con éxito.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Cerrar"></button>
            </div>
        </div>
    </div>
    
</body>

</html>