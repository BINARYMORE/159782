<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../inicio_sesion.html");
    exit();
}
?>
