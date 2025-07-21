<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Debes iniciar sesión.'); window.location.href='../inicio_sesion.html';</script>";
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if (!isset($_GET['curso_id'])) {
    echo "Curso no especificado.";
    exit;
}

$curso_id = $_GET['curso_id'];

// Verificar que el curso existe
$stmt = $pdo->prepare("SELECT * FROM curso WHERE id = ?");
$stmt->execute([$curso_id]);
$curso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    echo "Curso no encontrado.";
    exit;
}

// Verificar datos del usuario (estado, plan y fecha activación)
$stmt = $pdo->prepare("SELECT estado, plan_id, fecha_activacion FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Si el usuario no existe o está inactivo, bloquear matrícula
if (!$usuario || $usuario['estado'] !== 'activo') {
    echo "<script>alert('No puedes matricularte porque tu cuenta está inactiva.'); window.location.href='Aprendizaje.php';</script>";
    exit;
}

// Verificar si el plan ha vencido
$fecha_activacion = $usuario['fecha_activacion'];
$fecha_actual = new DateTime();
$vencido = false;

if ($fecha_activacion) {
    $fecha_activacion_dt = new DateTime($fecha_activacion);
    $dias = $fecha_activacion_dt->diff($fecha_actual)->days;

    if ($dias >= 30) {
        $vencido = true;
    }
}

if ($vencido) {
    // Desactivar al usuario
    $stmt = $pdo->prepare("UPDATE usuarios SET estado = 'inactivo', plan_id = NULL, fecha_activacion = NULL WHERE id = ?");
    $stmt->execute([$usuario_id]);

    // Eliminar sus matrículas
    $stmt = $pdo->prepare("DELETE FROM curso_usuario WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);

    echo "<script>alert('Tu plan ha vencido. Por favor, vuelve a suscribirte.'); window.location.href='Aprendizaje.php';</script>";
    exit;
}

// Verificar si ya está matriculado
$stmt = $pdo->prepare("SELECT * FROM curso_usuario WHERE curso_id = ? AND usuario_id = ?");
$stmt->execute([$curso_id, $usuario_id]);

if ($stmt->fetch()) {
    echo "<script>alert('Ya estás matriculado en este curso.'); window.location.href='Aprendizaje.php';</script>";
    exit;
}

// Validar límite de cursos según plan
$plan_id = $usuario['plan_id'];

if ($plan_id == 1) {
    $limite = 1; // Básico
} elseif ($plan_id == 2) {
    $limite = 2; // Intermedio
} else {
    $limite = -1; // Premium (sin límite)
}

// Contar cursos ya matriculados
$stmt = $pdo->prepare("SELECT COUNT(*) FROM curso_usuario WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$cantidad_matriculados = $stmt->fetchColumn();

if ($limite != -1 && $cantidad_matriculados >= $limite) {
    echo "<script>alert('Has alcanzado el límite de cursos según tu plan.'); window.location.href='Aprendizaje.php';</script>";
    exit;
}

// Insertar matrícula con fecha
$stmt = $pdo->prepare("INSERT INTO curso_usuario (curso_id, usuario_id, fecha_matricula) VALUES (?, ?, NOW())");
$stmt->execute([$curso_id, $usuario_id]);

echo "<script>alert('Te has matriculado correctamente en el curso.'); window.location.href='Aprendizaje.php';</script>";
exit;
?>
