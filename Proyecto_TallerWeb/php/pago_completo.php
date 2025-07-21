<?php
require 'Conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $nombre = $_POST['nombre'];
    $plan_nombre = $_POST['plan'];
    $metodo_pago = $_POST['metodo_pago'];

    try {
        $stmtUser = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmtUser->execute([$correo]);
        $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            echo "<script>
                alert('El correo no está registrado.');
                window.history.back();
            </script>";
            exit;
        }

        $id_usuario = $usuario['id'];

        $stmtPlan = $pdo->prepare("SELECT id, monto FROM planes WHERE nombre = ?");
        $stmtPlan->execute([$plan_nombre]);
        $plan = $stmtPlan->fetch(PDO::FETCH_ASSOC);

        if (!$plan) {
            echo "<script>
                alert('El plan seleccionado no es válido.');
                window.history.back();
            </script>";
            exit;
        }

        $plan_id = $plan['id'];
        $monto = $plan['monto'];

        $insertPago = $pdo->prepare("INSERT INTO pagos (id_usuario, monto, fecha_pago, metodo_pago, estado_pago) VALUES (?, ?, NOW(), ?, 'completado')");
        $insertPago->execute([$id_usuario, $monto, $metodo_pago]);

        $updateUsuario = $pdo->prepare("UPDATE usuarios SET plan_id = ?, estado = 'activo', fecha_activacion = CURDATE() WHERE id = ?");
        $updateUsuario->execute([$plan_id, $id_usuario]);

        echo "<script>
            alert('Pago registrado y plan asignado con éxito');
            window.location.href = '../inicio_sesion.html';
        </script>";

    } catch (PDOException $e) {
        $error = addslashes($e->getMessage());
        echo "<script>
            alert('Error en el pago: $error');
            window.history.back();
        </script>";
    }
}
?>
