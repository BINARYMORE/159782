<?php
session_start();
require 'Conexion.php'; 

if (!isset($_SESSION['profesor_id'])) {
    echo "<script>alert('Debes iniciar sesión primero'); window.location.href='../inicio_sesion.html';</script>";
    exit;
}

$profesor_id = $_SESSION['profesor_id'];

$stmt = $pdo->prepare("SELECT * FROM profesor WHERE id = ?");
$stmt->execute([$profesor_id]);
$profesor = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Profesor</title>
    <link rel="stylesheet" href="../css/Perfil_profesor.css">
</head>
<body>

<header class="header">
    <a href="#" class="logo">SmartClass</a>
    <input type="checkbox" id="menu">
    <label for="menu" class="menu-label">
        <img src="../Imagenes/Menu.png" class="menu-icono" alt="Menú">
    </label>
    <nav class="navbar">
        <a href="../Dashboard.html">Inicio</a>
        <a href="agregar_pdf.php">Material</a>
        <a href="agregar_zoom.php">Zoom</a>
        <a href="lista_alumnos.php">Lista</a>
        <a href="Perfil_profesor.php">Perfil</a>
    </nav>
</header>

    <div class="dashboard-bar">
        PERFIL DEL PROFESOR
    </div>

    <main class="perfil-container">
        <aside class="perfil-sidebar">
            <div class="perfil-avatar">
                <img src="../imagenes/perfil.png" alt="Foto del profesor">
            </div>
            <div class="perfil-opciones">
                <button onclick="window.location.href='cerrar_sesion.php'">Cerrar Sesión</button>
            </div>
        </aside>

        <section class="perfil-contenido">
            <div class="perfil-header">
                <h2>Información Personal</h2>
                <p><strong>Nombre:</strong> <?= htmlspecialchars($profesor['nombre']) ?></p>
                <p><strong>Apellido:</strong> <?= htmlspecialchars($profesor['apellido']) ?></p>
                <p><strong>Correo:</strong> <?= htmlspecialchars($profesor['correo']) ?></p>
                <p><strong>Especialidad:</strong> <?= htmlspecialchars($profesor['especialidad']) ?></p>
                <p><strong>Teléfono:</strong> <?= htmlspecialchars($profesor['telefono']) ?></p>
                <p><strong>Estado:</strong> <?= htmlspecialchars($profesor['estado']) ?></p>
                <p><strong>Fecha de Registro:</strong> <?= htmlspecialchars($profesor['fecha_registro']) ?></p>
            </div>
        </section>
    </main>
</body>
</html>
