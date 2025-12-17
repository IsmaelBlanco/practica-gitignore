<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefono = $_POST['telefono'] ?? '';
    $mail = $_POST['mail'] ?? '';
    $horario = $_POST['horario'] ?? [];
    $lat = isset($_POST['lat']) ? floatval($_POST['lat']) : 0;
    $lng = isset($_POST['lng']) ? floatval($_POST['lng']) : 0;

    if (!$telefono || !$mail || empty($horario) || !isset($_POST['lat']) || !isset($_POST['lng'])) {
        die('Faltan datos obligatorios.');
    }


    $data = [
        'telefono' => $telefono,
        'mail' => $mail,
        'horario' => $horario,
        'coordenadas' => [
            'lat' => $lat,
            'lng' => $lng
        ]
    ];

    $filePath = '../data/info.json';

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($filePath, $json) !== false) {
        header('Location: /pages/edtinf?success=1');
        exit;
    } else {
        die('Error al guardar el archivo.');
    }
}
