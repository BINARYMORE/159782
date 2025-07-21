<?php
require 'Conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $ocupacion = $_POST['ocupacion'];
    $celular = $_POST['celular'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena']; 
    $estado_civil = $_POST['estado_civil'];

    $fechaLimite = '2015-12-31';
    if ($fecha_nacimiento > $fechaLimite) {
        echo "<script>
            alert('La fecha de nacimiento no valida');
            window.history.back();
        </script>";
        exit;
    }

    $verCorreo = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $verCorreo->execute([$correo]);
    if ($verCorreo->rowCount() > 0) {
        echo "<script>
            alert('El correo ya está registrado');
            window.history.back();
        </script>";
        exit;
    }

    $verCelular = $pdo->prepare("SELECT id FROM usuarios WHERE celular = ?");
    $verCelular->execute([$celular]);
    if ($verCelular->rowCount() > 0) {
        echo "<script>
            alert('El número de celular ya está registrado');
            window.history.back();
        </script>";
        exit;
    }

    $estado = 'inactivo';

    $sql = "INSERT INTO usuarios (
        nombre, apellidos, fecha_nacimiento, ocupacion,
        celular, correo, contrasena, estado_civil, estado
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $nombre, $apellidos, $fecha_nacimiento, $ocupacion,
            $celular, $correo, $contrasena, $estado_civil, $estado
        ]);

        echo "<script>
            alert('Registro exitoso');
            window.location.href = '../planes.html';
        </script>";

    } catch (PDOException $e) {
        $error = addslashes($e->getMessage());
        echo "<script>
            alert('Error al registrar: $error');
            window.history.back();
        </script>";
    }
}
?>
