<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['profesor_id'])) {
    echo "Debes iniciar sesión.";
    exit;
}

$profesor_id = $_SESSION['profesor_id'];

// Obtener curso del profesor
$stmt = $pdo->prepare("SELECT * FROM curso WHERE profesor_id = ?");
$stmt->execute([$profesor_id]);
$curso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$curso) {
    echo "No se encontró curso asignado al profesor.";
    exit;
}

$curso_id = $curso['id'];

$editando = false;
$edit_id = null;
$semana_id = '';
$tema = '';
$link_zoom = '';

// AGREGAR o ACTUALIZAR
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $semana_id = $_POST['semana'];
    $tema = $_POST['tema'];
    $link_zoom = $_POST['link_zoom'];

    if (isset($_POST['edit_id']) && $_POST['edit_id'] != '') {
        // Actualizar clase
        $edit_id = $_POST['edit_id'];
        $stmt = $pdo->prepare("UPDATE clase SET semana = ?, tema = ?, link_zoom = ? WHERE id = ? AND curso_id = ?");
        $stmt->execute([$semana_id, $tema, $link_zoom, $edit_id, $curso_id]);
    } else {
        // Validar que no exista clase con esa semana
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM clase WHERE semana = ? AND curso_id = ?");
        $stmt->execute([$semana_id, $curso_id]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO clase (curso_id, semana, tema, link_zoom) VALUES (?, ?, ?, ?)");
            $stmt->execute([$curso_id, $semana_id, $tema, $link_zoom]);
        } else {
            echo "<script>alert('Ya existe una clase para esta semana.');</script>";
        }
    }

    header("Location: Agregar_zoom.php");
    exit;
}

// ELIMINAR
if (isset($_GET['eliminar'])) {
    $idEliminar = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM clase WHERE id = ? AND curso_id = ?");
    $stmt->execute([$idEliminar, $curso_id]);
    header("Location: Agregar_zoom.php");
    exit;
}

// EDITAR
if (isset($_GET['editar'])) {
    $edit_id = $_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM clase WHERE id = ? AND curso_id = ?");
    $stmt->execute([$edit_id, $curso_id]);
    $claseEditar = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($claseEditar) {
        $editando = true;
        $semana_id = $claseEditar['semana'];
        $tema = $claseEditar['tema'];
        $link_zoom = $claseEditar['link_zoom'];
    }
}

// OBTENER semanas disponibles (no repetidas en clase)
$stmt = $pdo->prepare("
    SELECT s.* 
    FROM semana s
    WHERE s.curso_id = ?
    AND NOT EXISTS (
        SELECT 1 FROM clase c WHERE c.semana = s.id AND c.curso_id = ?
    )
    ORDER BY s.id
");
$stmt->execute([$curso_id, $curso_id]);
$semanas_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si se está editando, asegurarse de incluir la semana actual
if ($editando && $semana_id) {
    $yaExiste = false;
    foreach ($semanas_disponibles as $s) {
        if ($s['id'] == $semana_id) {
            $yaExiste = true;
            break;
        }
    }
    if (!$yaExiste) {
        $stmt = $pdo->prepare("SELECT * FROM semana WHERE id = ?");
        $stmt->execute([$semana_id]);
        $semanaActual = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($semanaActual) {
            array_unshift($semanas_disponibles, $semanaActual);
        }
    }
}

// Obtener todas las clases del curso
$stmt = $pdo->prepare("
    SELECT c.*, s.titulo AS semana_titulo
    FROM clase c
    INNER JOIN semana s ON c.semana = s.id
    WHERE c.curso_id = ?
    ORDER BY s.id
");
$stmt->execute([$curso_id]);
$clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión Zoom - <?php echo htmlspecialchars($curso['nombre']); ?></title>
    <link rel="stylesheet" href="../css/Zoom.css">
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
        <a href="Agregar_zoom.php">Zoom</a>
        <a href="lista_alumnos.php" class="active">Lista</a>
        <a href="Perfil_profesor.php">Perfil</a>
    </nav>
</header>

<div class="dashboard-bar">
    Gestión de enlaces Zoom
</div>

<div class="container">
    <h2><?php echo $editando ? "Editar enlace Zoom" : "Agregar enlace Zoom"; ?></h2>

    <form method="post">
        <input type="hidden" name="edit_id" value="<?php echo $editando ? $edit_id : ''; ?>">

        <label>Semana:</label>
        <select name="semana" required>
            <option value="">Selecciona semana</option>
            <?php foreach ($semanas_disponibles as $s): ?>
                <option value="<?php echo $s['id']; ?>" <?php echo ($s['id'] == $semana_id) ? 'selected' : ''; ?>>
                    Semana <?php echo $s['id']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Tema:</label>
        <input type="text" name="tema" required value="<?php echo htmlspecialchars($tema); ?>">

        <label>Enlace Zoom:</label>
        <input type="url" name="link_zoom" required value="<?php echo htmlspecialchars($link_zoom); ?>">

        <button type="submit"><?php echo $editando ? "Actualizar Zoom" : "Agregar Zoom"; ?></button>
    </form>

    <h2>Zooms Agregados</h2>
    <div class="zoom-cards">
        <?php foreach ($clases as $clase): ?>
            <div class="zoom-card">
                <h4>Semana <?php echo htmlspecialchars($clase['semana']); ?> - <?php echo htmlspecialchars($clase['semana_titulo']); ?></h4>
                <p><strong>Tema:</strong> <?php echo htmlspecialchars($clase['tema']); ?></p>
                <p><strong>Zoom:</strong> <a href="<?php echo htmlspecialchars($clase['link_zoom']); ?>" target="_blank">Ver enlace</a></p>
                <p>
                    <a href="Agregar_zoom.php?editar=<?php echo $clase['id']; ?>">✏️ Editar</a> |
                    <a href="Agregar_zoom.php?eliminar=<?php echo $clase['id']; ?>" onclick="return confirm('¿Eliminar este enlace Zoom?')">🗑️ Eliminar</a>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
