<?php
session_start(); // Inicia la sesión

require 'conexion.php'; // Incluye la conexión a la base de datos

// Verifica si el profesor ha iniciado sesión
if (!isset($_SESSION['profesor_id'])) {
    echo "Debes iniciar sesión.";
    exit;
}

$profesor_id = $_SESSION['profesor_id']; // Obtiene el ID del profesor de la sesión

// Consulta el curso asociado al profesor
$stmt = $pdo->prepare("SELECT * FROM curso WHERE profesor_id = ?");
$stmt->execute([$profesor_id]);
$curso = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica si se encontró el curso
if (!$curso) {
    echo "No se encontró curso asignado al profesor.";
    exit;
}

$curso_id = $curso['id']; // Obtiene el ID del curso
$editando = false;        // Bandera para saber si se está editando
$edit_id = null;
$semana_id = '';
$descripcion = '';
$archivo_pdf = '';

// Obtiene las semanas del curso ordenadas por título
$stmt = $pdo->prepare("SELECT * FROM semana WHERE curso_id = ? ORDER BY titulo ASC");
$stmt->execute([$curso_id]);
$semanas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si se envió el formulario (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $semana_id = $_POST['semana_id'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');
    $archivo_pdf = trim($_POST['archivo_pdf'] ?? '');

    // Si se está editando material existente
    if (isset($_POST['edit_id']) && $_POST['edit_id'] != '') {
        $edit_id = $_POST['edit_id'];

        // Actualiza el material si pertenece a una semana del curso actual
        $stmt = $pdo->prepare("UPDATE material SET semana_id = ?, descripcion = ?, archivo_pdf = ? WHERE id = ? AND semana_id IN (SELECT id FROM semana WHERE curso_id = ?)");
        $stmt->execute([$semana_id, $descripcion, $archivo_pdf, $edit_id, $curso_id]);
    
    } else {
        // Verifica si ya existe material en esa semana
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM material m INNER JOIN semana s ON m.semana_id = s.id WHERE m.semana_id = ? AND s.curso_id = ?");
        $stmtCheck->execute([$semana_id, $curso_id]);
        $existe = $stmtCheck->fetchColumn();

        if ($existe == 0) {
            // Inserta nuevo material
            $stmt = $pdo->prepare("INSERT INTO material (semana_id, descripcion, archivo_pdf) VALUES (?, ?, ?)");
            $stmt->execute([$semana_id, $descripcion, $archivo_pdf]);
        } else {
            // Si ya existe, muestra alerta
            echo "<script>alert('Ya existe material para esta semana.');window.location.href='agregar_pdf.php';</script>";
            exit;
        }
    }

    // Redirige luego de insertar o editar
    header("Location: agregar_pdf.php");
    exit;
}

// Si se está eliminando un material
if (isset($_GET['eliminar'])) {
    $idEliminar = $_GET['eliminar'];

    // Elimina solo si pertenece al curso del profesor
    $stmt = $pdo->prepare("DELETE m FROM material m INNER JOIN semana s ON m.semana_id = s.id WHERE m.id = ? AND s.curso_id = ?");
    $stmt->execute([$idEliminar, $curso_id]);

    header("Location: agregar_pdf.php");
    exit;
}

// Si se quiere editar un material
if (isset($_GET['editar'])) {
    $edit_id = $_GET['editar'];

    // Selecciona los datos del material a editar
    $stmt = $pdo->prepare("SELECT m.*, s.curso_id FROM material m INNER JOIN semana s ON m.semana_id = s.id WHERE m.id = ? AND s.curso_id = ?");
    $stmt->execute([$edit_id, $curso_id]);
    $materialEditar = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($materialEditar) {
        $editando = true;
        $semana_id = $materialEditar['semana_id'];
        $descripcion = $materialEditar['descripcion'];
        $archivo_pdf = $materialEditar['archivo_pdf'];
    }
}

// Obtiene todos los materiales del curso con el nombre de la semana
$stmt = $pdo->prepare("SELECT m.id, m.descripcion, m.archivo_pdf, s.titulo AS semana_titulo FROM material m INNER JOIN semana s ON m.semana_id = s.id WHERE s.curso_id = ? ORDER BY s.titulo ASC");
$stmt->execute([$curso_id]);
$materiales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML empieza aquí -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Material PDF - <?= htmlspecialchars($curso['nombre']) ?></title>
    <link rel="stylesheet" href="../css/agregar_pdf.css" />
</head>
<body>

<!-- Barra superior -->
<header class="header">
    <a href="#" class="logo">SmartClass</a>
    <input type="checkbox" id="menu" />
    <label for="menu" class="menu-label">
        <img src="../Imagenes/Menu.png" class="menu-icono" alt="Menú" />
    </label>
    <nav class="navbar">
        <a href="../Dashboard.html">Inicio</a>
        <a href="agregar_pdf.php">Material</a>
        <a href="Agregar_zoom.php">Zoom</a>
        <a href="lista_alumnos.php">Lista</a>
        <a href="Perfil_profesor.php">Perfil</a>
    </nav>
</header>

<!-- Título de la sección -->
<div class="dashboard-bar">Material del curso</div>

<div class="container">
    <h2><?= $editando ? "Editar material PDF" : "Agregar material PDF" ?></h2>

    <!-- Formulario para agregar/editar material -->
    <form method="post">
        <input type="hidden" name="edit_id" value="<?= $editando ? $edit_id : '' ?>" />

        <label for="semana_id">Semana:</label>
        <select name="semana_id" id="semana_id" required>
            <option value="">-- Selecciona Semana --</option>
            <?php foreach ($semanas as $sem): ?>
                <option value="<?= $sem['id'] ?>" <?= ($sem['id'] == $semana_id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sem['titulo']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="descripcion">Descripción:</label>
        <input type="text" name="descripcion" id="descripcion" required value="<?= htmlspecialchars($descripcion) ?>" />

        <label for="archivo_pdf">Link del PDF (Google Drive):</label>
        <input type="url" name="archivo_pdf" id="archivo_pdf" required value="<?= htmlspecialchars($archivo_pdf) ?>" />

        <button type="submit"><?= $editando ? "Actualizar PDF" : "Agregar PDF" ?></button>
    </form>

    <!-- Lista de materiales ya agregados -->
    <h2>Materiales Agregados</h2>
    <div class="material-cards">
        <?php foreach ($materiales as $mat): ?>
            <div class="material-card">
                <h4><?= htmlspecialchars($mat['semana_titulo']) ?></h4>
                <p><strong>Descripción:</strong> <?= htmlspecialchars($mat['descripcion']) ?></p>
                <p><strong>Archivo:</strong> 
                    <a href="<?= htmlspecialchars($mat['archivo_pdf']) ?>" target="_blank">Ver PDF</a>
                </p>
                <p>
                    <a href="agregar_pdf.php?editar=<?= $mat['id'] ?>">✏️ Editar</a> |
                    <a href="agregar_pdf.php?eliminar=<?= $mat['id'] ?>" onclick="return confirm('¿Eliminar este material?')">🗑️ Eliminar</a>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
