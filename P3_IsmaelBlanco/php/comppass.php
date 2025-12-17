<?php
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

require_once "../php/conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !$isAdmin) {
    $stmt = $pdo->prepare("SELECT clave FROM usuarios");
    $stmt->execute();
    $hash = $stmt->fetchColumn();

    if ($hash && password_verify($_POST['password'], $hash)) {
        $_SESSION['admin'] = true;
        header("Location: /");
        exit;
    } else {
        $error = "Clave incorrecta.";
        header("Location: /");
        exit;
    }
}