<?php

$host = 'localhost'; //meter dns
$port = '3306';
$db = 'concesionario';
$user = 'root'; //cambiar a usuario
$pass = 'Usuario@1'; //cambiar a usuario
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die('Error en la conexión a la base de datos: ' . $e->getMessage());
}
