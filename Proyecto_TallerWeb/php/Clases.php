<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Debes iniciar sesión'); location.href='inicio_sesion.html';</script>";
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener cursos en los que el usuario está matriculado
$stmt = $pdo->prepare("
    SELECT curso.id, curso.nombre, profesor.nombre AS profesor
    FROM curso
    JOIN curso_usuario ON curso.id = curso_usuario.curso_id
    JOIN profesor ON curso.profesor_id = profesor.id
    WHERE curso_usuario.usuario_id = ?
");
$stmt->execute([$usuario_id]);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener semanas por curso
function obtenerSemanas($pdo, $curso_id) {
    $stmt = $pdo->prepare("SELECT * FROM semana WHERE curso_id = ?");
    $stmt->execute([$curso_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener materiales por semana
function obtenerMateriales($pdo, $semana_id) {
    $stmt = $pdo->prepare("SELECT * FROM material WHERE semana_id = ?");
    $stmt->execute([$semana_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para convertir links de Google Drive a descarga directa
function convertirGoogleDrive($url) {
    if (preg_match('/\/file\/d\/([^\/]+)\//', $url, $match)) {
        return "https://drive.google.com/uc?export=download&id=" . $match[1];
    }
    return $url;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartClass - Mi Aprendizaje</title>
    <link rel="stylesheet" href="../css/Clases.css">
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

<div class="dashboard-bar">MIS CLASES</div>

<div class="contenedor">
    <div class="cursos">
        <?php foreach ($cursos as $curso): ?>
            <div class="card" onclick="toggleContenido(<?= $curso['id'] ?>)">
                <div class="img-card uno"></div>
                <h3><?= htmlspecialchars($curso['nombre']) ?></h3>
                <div class="profesor"><?= htmlspecialchars($curso['profesor']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="contenidos-cursos">
        <?php foreach ($cursos as $curso): ?>
            <div class="contenido-curso" id="contenido-<?= $curso['id'] ?>" style="display: none;">
                <?php
                $semanas = obtenerSemanas($pdo, $curso['id']);
                if (empty($semanas)) {
                    echo "<p><em>No hay contenido aún.</em></p>";
                } else {
                    foreach ($semanas as $semana) {
                        echo '<button class="acordeon">' . htmlspecialchars($semana['titulo']) . '</button>';
                        echo '<div class="panel">';

                        $materiales = obtenerMateriales($pdo, $semana['id']);
                        if (empty($materiales)) {
                            echo "<p>Sin materiales.</p>";
                        } else {
                            foreach ($materiales as $material) {
                                echo "<p>" . htmlspecialchars($material['descripcion']);

                                if (!empty($material['archivo_pdf'])) {
                                    $downloadLink = convertirGoogleDrive($material['archivo_pdf']);
                                    echo " <a class='descarga' href='" . htmlspecialchars($downloadLink) . "' target='_blank'>📄 Descargar PDF</a>";
                                }

                                echo "</p>";
                            }
                        }

                        echo '</div>';
                    }
                }
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function toggleContenido(id) {
        const contenido = document.getElementById('contenido-' + id);
        document.querySelectorAll('.contenido-curso').forEach(el => {
            if (el !== contenido) el.style.display = 'none';
        });
        contenido.style.display = (contenido.style.display === 'block') ? 'none' : 'block';
        if (contenido.style.display === 'block') {
            contenido.scrollIntoView({ behavior: 'smooth' });
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        const acordeones = document.querySelectorAll(".acordeon");
        acordeones.forEach(btn => {
            btn.addEventListener("click", function () {
                const panel = this.nextElementSibling;
                document.querySelectorAll(".panel").forEach(p => {
                    if (p !== panel) p.style.display = "none";
                });
                panel.style.display = panel.style.display === "block" ? "none" : "block";
            });
        });
    });
</script>

</body>
</html>
