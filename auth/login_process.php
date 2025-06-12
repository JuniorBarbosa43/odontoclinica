<?php
// auth/login_process.php

// Garante que o método seja POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Inclui os arquivos necessários
    require_once '../core/db_connection.php';
    require_once '../controllers/auth_controller.php';

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Tenta realizar o login
    if (attempt_login($conn, $email, $senha)) {
        // Redireciona para o dashboard em caso de sucesso
        header("Location: ../views/dashboard.php");
        exit();
    } else {
        // Redireciona de volta para o login com uma mensagem de erro
        header("Location: ../views/login.php?error=1");
        exit();
    }
} else {
    // Se não for POST, redireciona para a página de login
    header("Location: ../views/login.php");
    exit();
}
?>