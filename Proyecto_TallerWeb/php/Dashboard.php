<?php
session_start();
$mostrar_popup = false;
$popup_tipo = '';
$dias_restantes = 0;

if (isset($_SESSION['estado'])) {
    if ($_SESSION['estado'] === 'activo' && isset($_SESSION['fecha_activacion'])) {
        $fecha_activacion = new DateTime($_SESSION['fecha_activacion']);
        $hoy = new DateTime();
        $diasPasados = $fecha_activacion->diff($hoy)->days;
        $dias_restantes = max(0, 30 - $diasPasados);

        $popup_tipo = 'bienvenida';
        $mostrar_popup = true;
    } elseif ($_SESSION['estado'] === 'inactivo') {
        $popup_tipo = 'expirado';
        $mostrar_popup = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/Dashboard.css">
</head>
<body>
    
<header class="header">
    <a href="#" class="logo">SmartClass</a>
    <input type="checkbox" id="menu">
    <label for="menu" class="menu-label">
        <img src="../Imagenes/Clases Menu.png" class="menu-icono" alt="Menú">
    </label>
    <nav class="navbar">
        <a href="Aprendizaje.php" class="active">Material</a>
        <a href="Clases.php">Zoom</a>
        <a href="Perfil.php">Lista</a>
        <a href="Perfil.php">Perfil</a>
    </nav>
</header>

    <div class="dashboard-bar">
        Dashboard
    </div>

    <section class="guias-estudio">
        <div class="contenedor-guias-titulo">
            <h2>Mi Trayectoria de Formación y Desarrollo Docente</h2>
        </div>
        <div class="contenedor-guias">
           <div class="contenedor-guias">
    <a class="guia" href="curso_java.php?curso=java">
        <span class="etiqueta formacion">Profesor</span>
        <h3>Subir nuevo material PDF</h3>
        <div class="icono-curso">
            <img src="../Imagenes/PDF.png" alt="">
        </div>
    </a>

    <a class="guia" href="curso_java.php?curso=bd">
        <span class="etiqueta formacion">Profesor</span>
        <h3>Gestionar enlaces de Zoom</h3>
        <div class="icono-curso">
            <img src="../Imagenes/Zoom.png" alt="">
        </div>
    </a>

    <a class="guia" href="curso_java.php?curso=html">
        <span class="etiqueta formacion">Profesor</span>
        <h3>Ver lista de alumnos inscritos</h3>
        <div class="icono-curso">
            <img src="../Imagenes/lista.png" alt="">
        </div>
    </a>

    <a class="guia" href="curso_java.php?curso=java">
        <span class="etiqueta formacion">Profesor</span>
        <h3>Ver mi perfil</h3>
        <div class="icono-curso">
            <img src="../Imagenes/Perfil01.png" alt="">
        </div>
    </a>
</div>
</body>
</html>
