<?php
session_start();
require 'Conexion.php';

// Habilitar modo de errores PDO
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Obtener datos del usuario y su seguridad
    $stmt = $pdo->prepare("
        SELECT u.*, s.intentos_fallidos, s.bloqueo_hasta, s.fecha_activacion AS seguridad_fecha_activacion
        FROM usuarios u
        LEFT JOIN seguridad_usuario s ON u.id = s.usuario_id
        WHERE u.correo = ?
    ");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $usuario_id = $usuario['id'];
        $planCancelado = false;

        // Si tiene plan activo, verificar si ya venció (más de 30 días)
        if ($usuario['estado'] === 'activo' && !empty($usuario['fecha_activacion'])) {
            $fechaActivacion = new DateTime($usuario['fecha_activacion']);
            $hoy = new DateTime();
            $diasPasados = $fechaActivacion->diff($hoy)->days;

            if ($diasPasados > 30) {
                // Cancelar plan y desactivar usuario
                $stmtInactivar = $pdo->prepare("UPDATE usuarios SET estado = 'inactivo', plan_id = NULL, fecha_activacion = NULL WHERE id = ?");
                $stmtInactivar->execute([$usuario_id]);

                // Limpiar fecha_activacion en seguridad_usuario
                $stmtResetSeguridad = $pdo->prepare("UPDATE seguridad_usuario SET fecha_activacion = NULL WHERE usuario_id = ?");
                $stmtResetSeguridad->execute([$usuario_id]);

                // Verificar si tiene cursos antes de intentar eliminar
                $stmtCheckExist = $pdo->prepare("SELECT COUNT(*) FROM curso_usuario WHERE usuario_id = ?");
                $stmtCheckExist->execute([$usuario_id]);
                $existenCursos = $stmtCheckExist->fetchColumn();

                if ($existenCursos > 0) {
                    $stmtEliminarCursos = $pdo->prepare("DELETE FROM curso_usuario WHERE usuario_id = ?");
                    if (!$stmtEliminarCursos->execute([$usuario_id])) {
                        $errorInfo = $stmtEliminarCursos->errorInfo();
                        echo "<script>alert('Error al eliminar matrículas: " . $errorInfo[2] . "');</script>";
                    } else {
                        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM curso_usuario WHERE usuario_id = ?");
                        $stmtCheck->execute([$usuario_id]);
                        $restantes = $stmtCheck->fetchColumn();
                        if ($restantes > 0) {
                            echo "<script>alert('Advertencia: No se eliminaron todas las matrículas.');</script>";
                        }
                    }
                }

                $planCancelado = true;
                $usuario['estado'] = 'inactivo';
                $usuario['plan_id'] = null;
                $usuario['fecha_activacion'] = null;
            }
        }

        // Verificar bloqueo
        if (!empty($usuario['bloqueo_hasta']) && strtotime($usuario['bloqueo_hasta']) > time()) {
            $minutos_restantes = ceil((strtotime($usuario['bloqueo_hasta']) - time()) / 60);
            echo "<script>
                alert('Cuenta bloqueada. Intenta en $minutos_restantes minutos.');
                window.history.back();
            </script>";
            exit;
        }

        // Reiniciar bloqueo si ya venció
        if (!empty($usuario['bloqueo_hasta']) && strtotime($usuario['bloqueo_hasta']) <= time()) {
            $stmtReset = $pdo->prepare("UPDATE seguridad_usuario SET intentos_fallidos = 0, bloqueo_hasta = NULL WHERE usuario_id = ?");
            $stmtReset->execute([$usuario_id]);
            $usuario['intentos_fallidos'] = 0;
            $usuario['bloqueo_hasta'] = null;
        }

        // Verificar contraseña
        if ($contrasena === $usuario['contrasena']) {
            $stmtReset = $pdo->prepare("UPDATE seguridad_usuario SET intentos_fallidos = 0, bloqueo_hasta = NULL WHERE usuario_id = ?");
            $stmtReset->execute([$usuario_id]);

            $_SESSION['usuario_id'] = $usuario_id;
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['estado'] = $usuario['estado'];
            $_SESSION['plan_id'] = $usuario['plan_id'];
            $_SESSION['fecha_activacion'] = $usuario['fecha_activacion'];

            if ($planCancelado) {
                echo "<script>
                    alert('Tu suscripción ha vencido y ha sido cancelada. Puedes volver a suscribirte.');
                    window.location.href = 'Aprendizaje.php';
                </script>";
            } else {
                echo "<script>
                    alert('Bienvenido');
                    window.location.href = 'Aprendizaje.php';
                </script>";
            }
            exit;
        } else {
            // Manejo de intentos fallidos
            $intentos = isset($usuario['intentos_fallidos']) ? $usuario['intentos_fallidos'] + 1 : 1;

            if ($intentos >= 4) {
                $bloqueoHasta = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                $stmt = $pdo->prepare("UPDATE seguridad_usuario SET intentos_fallidos = ?, bloqueo_hasta = ? WHERE usuario_id = ?");
                $stmt->execute([$intentos, $bloqueoHasta, $usuario_id]);

                echo "<script>
                    alert('Cuenta bloqueada por 15 minutos.');
                    window.history.back();
                </script>";
            } else {
                $stmt = $pdo->prepare("UPDATE seguridad_usuario SET intentos_fallidos = ? WHERE usuario_id = ?");
                $stmt->execute([$intentos, $usuario_id]);

                echo "<script>
                    alert('Contraseña incorrecta. Intento $intentos de 4.');
                    window.history.back();
                </script>";
            }
            exit;
        }
    } else {
        // Verificar si es un profesor
        $stmt = $pdo->prepare("SELECT * FROM profesor WHERE correo = ?");
        $stmt->execute([$correo]);
        $profesor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($profesor && $contrasena === $profesor['contrasena']) {
            $_SESSION['profesor_id'] = $profesor['id'];
            $_SESSION['nombre'] = $profesor['nombre'];

            echo "<script>
                alert('Bienvenido profesor');
                window.location.href = '../Dashboard.html';
            </script>";
            exit;
        } else {
            echo "<script>
                alert('Correo o contraseña incorrectos');
                window.history.back();
            </script>";
            exit;
        }
    }
}
?>
