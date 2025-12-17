<?php
require_once '../php/conexion.php';
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['marcas']) || empty($data['marcas'])) {
    echo json_encode([]);
    exit;
}

$marcas = $data['marcas'];
$inQuery = implode(',', array_fill(0, count($marcas), '?'));
$sql = "SELECT DISTINCT modelo FROM coches WHERE marca IN ($inQuery) ORDER BY modelo";

$stmt = $pdo->prepare($sql);
$stmt->execute($marcas);
$modelos = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($modelos);