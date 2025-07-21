<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'Conexion.php';

if (!isset($_SESSION['profesor_id'])) {
    echo "Acceso denegado.";
    exit;
}

$profesor_id = $_SESSION['profesor_id'];

$stmtCursos = $pdo->prepare("SELECT * FROM curso WHERE profesor_id = ?");
$stmtCursos->execute([$profesor_id]);
$cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Alumnos</title>
    <link rel="stylesheet" href="../css/lista_alumnos.css">
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
        <a href="lista_alumnos.php" class="active">Lista</a>
        <a href="Perfil_profesor.php">Perfil</a>
    </nav>
</header>

<div class="dashboard-bar">
    Lista de alumnos
</div>

<?php foreach ($cursos as $curso): ?>
    <h2 style="text-align:center;">Revise la lista de estudiantes inscritos</h2>

    <?php
    $stmtAlumnos = $pdo->prepare("
        SELECT u.nombre, u.apellidos, u.correo
        FROM curso_usuario cu
        JOIN usuarios u ON cu.usuario_id = u.id
        WHERE cu.curso_id = ?
    ");
    $stmtAlumnos->execute([$curso['id']]);
    $alumnos = $stmtAlumnos->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (count($alumnos) === 0): ?>
        <p style="text-align:center;">No hay alumnos matriculados en este curso.</p>
    <?php else: ?>
        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellidos</th>
                        <th>Correo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos as $alumno): ?>
                        <tr>
                            <td><?= htmlspecialchars($alumno['nombre']) ?></td>
                            <td><?= htmlspecialchars($alumno['apellidos']) ?></td>
                            <td><?= htmlspecialchars($alumno['correo']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

</body>
</html>
