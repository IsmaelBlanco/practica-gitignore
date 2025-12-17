<?php
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
require_once "../php/conexion.php";

if (!isset($_SESSION['access_token']) || !isset($_GET['token']) || $_GET['token'] !== $_SESSION['access_token']) {
    header("Location: /pages/error");
    exit;
}

unset($_SESSION['access_token']);

// $clave_plana = '479130'; 
// $hash = password_hash($clave_plana, PASSWORD_DEFAULT);
// $stmt = $pdo->prepare("INSERT INTO usuarios (clave) VALUES (:clave)");
// $stmt->execute(['clave' => $hash]);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link rel="icon" href="/src/logo.png" type="image/png">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/loginstyle.css">
    <script src="../js/bootstrap.bundle.min.js" defer></script>
    <title>Acceso | CarSolucion</title>
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
        </div>
    </header>

    <main>
        <div class="loginCard">
            <?php if (!$isAdmin): ?>
                <h1>Acceso</h1>
                <form method="POST" action="../php/comppass.php">
                    <div>
                        <label for="password">Clave de acceso:</label>
                        <input id="password" type="password" placeholder="********" name="password" required>
                    </div>
                    <button type="submit">Entrar</button>
                </form>
                <?php if (isset($error)): ?>
                    <p style="color:red; margin-top: 1rem;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
            <?php else: ?>
                <h1>Ya estás logeado</h1>
                <form method="POST" action="../php/logout.php">
                    <div>
                        <h3>¿Quieres cerrar sesión?</h3>
                    </div>
                    <button type="submit" class="logout">Cerrar sesión</button>
                </form>
                <a href="/" class="volver-btn">Volver</a>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>