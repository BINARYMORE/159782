<?php
session_start();
require 'Conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../inicio_sesion.html");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Editar Perfil</title>
  <link rel="stylesheet" href="../css/Editar.css" />
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

  <div class="dashboard-bar">EDITAR PERFIL</div>

  <div class="perfil-container">
    <section class="perfil-contenido">
      <h2 style="margin-bottom: 2rem;">Editar Información Personal</h2>

      <form class="formulario-editar" action="procesar_edicion.php" method="POST">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" required />

        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($usuario['apellidos'] ?? '') ?>" required />

        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($usuario['fecha_nacimiento'] ?? '') ?>" required />

        <label for="ocupacion">Ocupación</label>
        <input type="text" id="ocupacion" name="ocupacion" value="<?= htmlspecialchars($usuario['ocupacion'] ?? '') ?>" />

        <label for="celular">Celular</label>
        <input type="tel" id="celular" name="celular" value="<?= htmlspecialchars($usuario['celular'] ?? '') ?>" />

        <label for="correo">Correo Electrónico</label>
        <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>" required />

        <label for="estado_civil">Estado Civil</label>
        <select id="estado_civil" name="estado_civil">
          <option value="Soltero" <?= ($usuario['estado_civil'] ?? '') == 'Soltero' ? 'selected' : '' ?>>Soltero</option>
          <option value="Casado" <?= ($usuario['estado_civil'] ?? '') == 'Casado' ? 'selected' : '' ?>>Casado</option>
          <option value="Otro" <?= ($usuario['estado_civil'] ?? '') == 'Otro' ? 'selected' : '' ?>>Otro</option>
        </select>

        <label for="biografia">Biografía</label>
        <textarea id="biografia" name="biografia" rows="5"><?= htmlspecialchars($usuario['biografia'] ?? '') ?></textarea>

        <button type="submit">Guardar Cambios</button>
      </form>
    </section>
  </div>
</body>
</html>
