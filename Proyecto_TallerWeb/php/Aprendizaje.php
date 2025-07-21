<?php
session_start();
require 'conexion.php'; 

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../inicio_sesion.html");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT nombre, estado, fecha_activacion FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$mostrar_popup = false;
$popup_tipo = '';
$dias_restantes = 0;

if ($usuario) {
    $nombre = $usuario['nombre'];
    $estado = $usuario['estado'];
    $fecha_activacion = $usuario['fecha_activacion'];

    if ($estado === 'activo' && $fecha_activacion) {
        $fecha_activacion_dt = new DateTime($fecha_activacion);
        $hoy = new DateTime();
        $diasPasados = $fecha_activacion_dt->diff($hoy)->days;
        $dias_restantes = max(0, 30 - $diasPasados);

        $popup_tipo = 'bienvenida';
        $mostrar_popup = true;
    } elseif ($estado === 'inactivo') {
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
    <title>Mi Aprendizaje</title>
    <link rel="stylesheet" href="../css/Aprendizaje.css">
</head>
<body>

<header class="header">
    <a href="#" class="logo">SmartClass</a>
    <input type="checkbox" id="menu">
    <label for="menu" class="menu-label">
        <img src="../Imagenes/Clases Menu.png" class="menu-icono" alt="Menú">
    </label>
    <nav class="navbar">
        <a href="claseszoom.php">Zoom</a>
        <a href="Clases.php">Material</a>
        <a href="Perfil.php">Perfil</a>
    </nav>
</header>

<div class="dashboard-bar">
    MI APRENDIZAJE
</div>

<section class="guias-estudio">
    <div class="contenedor-guias-titulo">
        <h2>Cursos Virtuales y Rutas de Aprendizaje que Estoy Siguiendo</h2>
    </div>
    <div class="contenedor-guias">
        <a class="guia" href="matricular_curso.php?curso_id=1">
            <span class="etiqueta formacion">Tutorías</span>
            <h3>Programación Orientada a Objetos con Java</h3>
            <div class="icono-curso">
                <img src="../Imagenes/Java.png" alt="">
            </div>
            <div class="progreso"><strong>Prof. Luis Ramírez</strong></div>
        </a>

        <a class="guia" href="matricular_curso.php?curso_id=2">
            <span class="etiqueta formacion">Tutorías</span>
            <h3>Administración y Modelado de Bases de Datos</h3>
            <div class="icono-curso">
                <img src="../Imagenes/Base de Dato.png" alt="">
            </div>
            <div class="progreso"><strong>Prof. Martín Salazar</strong></div>
        </a>

        <a class="guia" href="matricular_curso.php?curso_id=3">
            <span class="etiqueta formacion">Tutorías</span>
            <h3>Diseño y Maquetación Web con HTML</h3>
            <div class="icono-curso">
                <img src="../Imagenes/Html.png" alt="">
            </div>
            <div class="progreso"><strong>Prof. Diego Herrera</strong></div>
        </a>
    </div>
</section>

<div class="overlay" id="popup" style="display: none;">
    <div class="popup">
        <button class="close-btn" onclick="document.getElementById('popup').style.display='none'">&times;</button>

        <?php if ($popup_tipo === 'bienvenida'): ?>
            <div class="emoji">👋</div>
            <strong>¡Bienvenido, <?= htmlspecialchars($nombre) ?>!</strong>
            <p>Te quedan <strong><?= $dias_restantes ?></strong> días de suscripción.</p>

        <?php elseif ($popup_tipo === 'expirado'): ?>
            <div class="emoji">😢</div>
            <strong>Tu suscripción ha terminado</strong>
            <p>Si disfrutaste nuestra plataforma, este es el momento ideal para regresar.</p>
            <button onclick="window.location.href='../planes.html'">Reactivar suscripción</button>
        <?php endif; ?>
    </div>
</div>

<?php if ($mostrar_popup): ?>
<script>
    window.onload = function () {
        document.getElementById("popup").style.display = "flex";
    };
</script>
<?php endif; ?>
</body>
</html>
