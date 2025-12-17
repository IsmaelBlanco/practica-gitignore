<?php
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
require_once "../php/conexion.php";

if (!($isAdmin)) {
    header('Location: /');
    exit;
}

$success = isset($_GET['success']) && $_GET['success'] == '1';

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/src/logo.png" type="image/png">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/adminareastyle.css">
    <script src="../js/bootstrap.bundle.min.js" defer></script>
    <title>Área de administración | CarSolucion </title>
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
        </div>
    </header>

    <main>
        <div class="optnsqr addcch">
            <p><a href="addcch">Subir un coche</a></p>
        </div>
        <div class="optnsqr chgcch">
            <p><a href="chgcch">Administrar catálogo</a></p>
        </div>
        <div class="optnsqr edtinf">
            <p><a href="edtinf">Editar información</a></p>
        </div>
    </main>
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055">
        <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive"
            aria-atomic="true" id="uploadToast">
            <div class="d-flex">
                <div class="toast-body">
                    Coche subido con éxito.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <?php if ($success): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const toastEl = document.getElementById('uploadToast');
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        </script>
    <?php endif; ?>
</body>

</html>