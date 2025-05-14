<?php
    if (session_status() === PHP_SESSION_NONE) {
    session_start();

    }
    require '../config/database.php';
    if (!isset($_SESSION['usuario_id'])) {
        // Redireciona para a página de login
        header('Location: login.php');
        exit();
    }

?>