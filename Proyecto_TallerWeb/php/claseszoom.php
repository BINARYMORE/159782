<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    echo "<script>alert('Debes iniciar sesión.'); window.location.href = '../inicio_sesion.html';</script>";
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener cursos del usuario
$stmt = $pdo->prepare("
    SELECT c.id, c.nombre 
    FROM curso c
    JOIN curso_usuario cu ON c.id = cu.curso_id
    WHERE cu.usuario_id = ?
");
$stmt->execute([$usuario_id]);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

function obtenerSemanas($pdo, $curso_id) {
    $stmt = $pdo->prepare("SELECT * FROM semana WHERE curso_id = ?");
    $stmt->execute([$curso_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerZoomPorSemana($pdo, $curso_id, $semana_id) {
    $stmt = $pdo->prepare("SELECT * FROM clase WHERE curso_id = ? AND semana = ?");
    $stmt->execute([$curso_id, $semana_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Zoom - SmartClass</title>
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

    <div class="dashboard-bar">ENLACES DE ZOOM</div>

    <div class="contenedor">
        <div class="cursos">
            <?php foreach ($cursos as $curso): ?>
                <div class="card" onclick="toggleContenido(<?= $curso['id'] ?>)">
                    <div class="img-card uno"></div>
                    <h3><?= htmlspecialchars($curso['nombre']) ?></h3>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="contenidos-cursos">
            <?php foreach ($cursos as $curso): ?>
                <div class="contenido-curso" id="contenido-<?= $curso['id'] ?>">
                    <?php
                    $semanas = obtenerSemanas($pdo, $curso['id']);
                    if (empty($semanas)) {
                        echo "<p><em>No hay semanas registradas.</em></p>";
                    } else {
                        foreach ($semanas as $semana) {
                            echo '<button class="acordeon">' . htmlspecialchars($semana['titulo']) . '</button>';
                            echo '<div class="panel">';

                            $clases = obtenerZoomPorSemana($pdo, $curso['id'], $semana['id']);
                            if (empty($clases)) {
                                echo "<p>No hay enlaces Zoom disponibles.</p>";
                            } else {
                                foreach ($clases as $clase) {
                                    echo "<p><strong>" . htmlspecialchars($clase['tema']) . ":</strong> ";
                                    echo "<a href='" . htmlspecialchars($clase['link_zoom']) . "' target='_blank' class='descarga'>Ir a Zoom</a></p>";
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
