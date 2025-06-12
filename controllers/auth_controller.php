<?php
// controllers/auth_controller.php

/**
 * Tenta autenticar um dentista com email e senha.
 *
 * @param mysqli $conn A conexão com o banco de dados.
 * @param string $email O email do dentista.
 * @param string $senha A senha do dentista.
 * @return bool True se a autenticação for bem-sucedida, false caso contrário.
 */
function attempt_login(mysqli $conn, string $email, string $senha): bool
{
    // Prepara a query para evitar SQL Injection
    $stmt = $conn->prepare("SELECT id, nome, senha FROM dentistas WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifica se a senha fornecida corresponde ao hash no banco
        if (password_verify($senha, $user['senha'])) {
            // Inicia a sessão e armazena os dados do usuário
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['logged_in'] = true;

            return true;
        }
    }

    return false;
}