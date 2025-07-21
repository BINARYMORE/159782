<?php
session_start();
require 'Conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../inicio_sesion.html");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$correo = $_POST['correo'];

$fecha_nacimiento = $_POST['fecha_nacimiento'];
$ocupacion = $_POST['ocupacion'];
$celular = $_POST['celular'];
$estado_civil = $_POST['estado_civil'];
$biografia = $_POST['biografia'];

try {
    // Actualizar tabla usuarios (solo los campos que realmente existen)
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, correo = ? WHERE id = ?");
    $stmt->execute([$nombre, $apellidos, $correo, $usuario_id]);

    // Verificar si ya hay datos en detalles_personales
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM detalles_personales WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $existe = $stmt->fetchColumn();

    if ($existe) {
        // Si ya existe, actualizar
        $stmt = $pdo->prepare("UPDATE detalles_personales SET 
            fecha_nacimiento = ?, ocupacion = ?, celular = ?, estado_civil = ?, biografia = ?
            WHERE usuario_id = ?");
        $stmt->execute([$fecha_nacimiento, $ocupacion, $celular, $estado_civil, $biografia, $usuario_id]);
    } else {
        // Si no existe, insertar nuevo registro
        $stmt = $pdo->prepare("INSERT INTO detalles_personales 
            (usuario_id, fecha_nacimiento, ocupacion, celular, estado_civil, biografia)
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $fecha_nacimiento, $ocupacion, $celular, $estado_civil, $biografia]);
    }

    header("Location: perfil.php");
    exit();
    
} catch (PDOException $e) {
    echo "Error al guardar: " . $e->getMessage();
}
?>
