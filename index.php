<?php
// index.php
session_start();

// Se o usuário já estiver logado, redireciona para o dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: views/dashboard.php');
    exit;
} else {
    // Caso contrário, redireciona para a página de login
    header('Location: views/login.php');
    exit;
}
?>