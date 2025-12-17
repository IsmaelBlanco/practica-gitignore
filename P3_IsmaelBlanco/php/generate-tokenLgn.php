<?php
session_start();
$token = bin2hex(random_bytes(16)); // token seguro
$_SESSION['access_token'] = $token;
echo json_encode(['token' => $token]);
