<?php
require_once "conexion.php";

header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['id']) || !isset($input['motivo'])) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

$id = (int)$input['id'];
$motivo = $input['motivo'];

if (!in_array($motivo, ['Vendido', 'Eliminado'])) {
    echo json_encode(["success" => false, "message" => "Motivo inválido"]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE coches SET estado = :motivo WHERE id = :id");
    $stmt->execute(['motivo' => $motivo, 'id' => $id]);

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
