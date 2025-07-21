<?php
session_start();
require 'conexion.php';

$usuario_id = $_SESSION['usuario_id'] ?? null;
$link = $_POST['link'] ?? null;
$estado = $_SESSION['estado'] ?? 'inactivo';
$plan = $_SESSION['plan'] ?? 'basico';

if (!$usuario_id || !$link) {
    exit("Error: Usuario no identificado o enlace faltante.");
}

if ($estado !== 'activo') {
    exit("Tu cuenta está inactiva. No puedes acceder a sesiones Zoom.");
}

$limite = match($plan) {
    'basico' => 2,
    'intermedio' => 4,
    'premium' => PHP_INT_MAX,
    default => 2
};


$lunes = date('Y-m-d', strtotime('monday this week'));

$stmt = $pdo->prepare("SELECT * FROM contador_zoom WHERE usuario_id = ? AND semana_inicio = ?");
$stmt->execute([$usuario_id, $lunes]);
$registro = $stmt->fetch(PDO::FETCH_ASSOC);

$cantidad = $registro['cantidad'] ?? 0;

if ($cantidad >= $limite) {
    echo "<h2 style='color:red;'>🔒 Has alcanzado el límite de $limite sesiones Zoom esta semana.</h2>";
    echo "<p><a href='javascript:history.back()'>Volver</a></p>";
    exit;
}

if ($registro) {
    $stmt = $pdo->prepare("UPDATE contador_zoom SET cantidad = cantidad + 1 WHERE id = ?");
    $stmt->execute([$registro['id']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO contador_zoom (usuario_id, semana_inicio, cantidad) VALUES (?, ?, 1)");
    $stmt->execute([$usuario_id, $lunes]);
}

$stmt = $pdo->prepare("INSERT INTO registro_zoom (usuario_id, fecha) VALUES (?, NOW())");
$stmt->execute([$usuario_id]);

header("Location: $link");
exit;
