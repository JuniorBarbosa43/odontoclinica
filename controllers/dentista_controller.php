<?php
// controllers/dentista_controller.php
session_start();
require_once '../core/db_connection.php';
require_once '../core/session_check.php';

$id_dentista = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'update_profile':
            $nome = htmlspecialchars(strip_tags($_POST['nome']));
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $cro = htmlspecialchars(strip_tags($_POST['cro']));

            if (!$email) {
                header("Location: ../views/perfil.php?status=error_email");
                exit();
            }

            $stmt = $conn->prepare("UPDATE dentistas SET nome = ?, email = ?, cro = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nome, $email, $cro, $id_dentista);

            if ($stmt->execute()) {
                // Atualiza o nome na sessão para refletir imediatamente na sidebar
                $_SESSION['user_name'] = $nome;
                header("Location: ../views/perfil.php?status=profile_updated");
            } else {
                header("Location: ../views/perfil.php?status=error");
            }
            $stmt->close();
            break;

        case 'update_password':
            $senha_atual = $_POST['senha_atual'];
            $nova_senha = $_POST['nova_senha'];
            $confirmar_nova_senha = $_POST['confirmar_nova_senha'];

            // 1. Validações básicas
            if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_nova_senha)) {
                header("Location: ../views/perfil.php?status=empty_fields");
                exit();
            }
            if ($nova_senha !== $confirmar_nova_senha) {
                header("Location: ../views/perfil.php?status=password_mismatch");
                exit();
            }
            if (strlen($nova_senha) < 6) {
                header("Location: ../views/perfil.php?status=password_short");
                exit();
            }

            // 2. Verifica a senha atual
            $stmt = $conn->prepare("SELECT senha FROM dentistas WHERE id = ?");
            $stmt->bind_param("i", $id_dentista);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($senha_atual, $user['senha'])) {
                // 3. Senha atual está correta, atualiza para a nova senha
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE dentistas SET senha = ? WHERE id = ?");
                $update_stmt->bind_param("si", $nova_senha_hash, $id_dentista);

                if ($update_stmt->execute()) {
                    header("Location: ../views/perfil.php?status=password_updated");
                } else {
                    header("Location: ../views/perfil.php?status=error");
                }
                $update_stmt->close();
            } else {
                // Senha atual incorreta
                header("Location: ../views/perfil.php?status=current_password_wrong");
            }
            $stmt->close();
            break;
    }
}

$conn->close();
exit();
?>