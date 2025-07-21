<?php
session_start();
require 'Conexion.php'; 

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Debes iniciar sesión primero'); window.location.href='../inicio_sesion.html';</script>";
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Función para convertir plan_id a nombre
function nombrePlan($id) {
    return match ($id) {
        1 => 'Básico',
        2 => 'Intermedio',
        3 => 'Premium',
        default => 'No asignado',
    };
}

// Consulta con JOIN a detalles_personales
$stmt = $pdo->prepare("SELECT u.*, d.fecha_nacimiento, d.estado_civil, d.ocupacion, d.celular, d.biografia
                       FROM usuarios u
                       LEFT JOIN detalles_personales d ON u.id = d.usuario_id
                       WHERE u.id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link rel="stylesheet" href="../css/Perfil.css">
</head>
<body>
<header class="header">
    <a href="#" class="logo">SmartClass</a>
    <input type="checkbox" id="menu">
    <label for="menu" class="menu-label">
        <img src="../Imagenes/Clases Menu.png" class="menu-icono" alt="Menú">
    </label>
    <nav class="navbar">
        <a href="Aprendizaje.php">Mi Aprendizaje</a>
        <a href="claseszoom.php">Zoom</a>
        <a href="Clases.php">Material</a>
        <a href="Perfil.php">Perfil</a>
    </nav>
</header>

    <div class="dashboard-bar">
        MI PERFIL
    </div>

    <main class="perfil-container">
        <aside class="perfil-sidebar">
            <div class="perfil-avatar">
                <img src="../imagenes/perfil.png" alt="Foto de perfil">
            </div>
            <div class="perfil-opciones">
                <button onclick="window.location.href='Editar_Perfil.php'">Editar Perfil</button>
            </div>
            <div class="perfil-opciones">
                <button onclick="window.location.href='cerrar_sesion.php'">Cerrar Sesión</button>
            </div>
        </aside>

        <section class="perfil-contenido">
            <div class="perfil-header">
                <h2>Información Personal</h2>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre'] ?? '') ?></p>
                <p><strong>Apellidos:</strong> <?= htmlspecialchars($usuario['apellidos'] ?? '') ?></p>
                <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars($usuario['fecha_nacimiento'] ?? 'No registrada') ?></p>
                <p><strong>Ocupación:</strong> <?= htmlspecialchars($usuario['ocupacion'] ?? 'No registrada') ?></p>
                <p><strong>Celular:</strong> <?= htmlspecialchars($usuario['celular'] ?? 'No registrado') ?></p>
                <p><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo'] ?? '') ?></p>
                <p><strong>Estado Civil:</strong> <?= htmlspecialchars($usuario['estado_civil'] ?? 'No registrado') ?></p>
                <p><strong>Plan:</strong> <?= nombrePlan($usuario['plan_id'] ?? 0) ?></p>
                <p><strong>Estado:</strong> <?= htmlspecialchars($usuario['estado'] ?? '') ?></p>
            </div>

            <div class="perfil-biografia">
                <h3>Biografía</h3>
                <p><?= !empty($usuario['biografia']) 
                    ? htmlspecialchars($usuario['biografia']) 
                    : "No has escrito una biografía aún." ?>
                </p>
            </div>
        </section>
    </main>
</body>
</html>
