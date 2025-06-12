<?php
session_start();
require_once '../core/db_connection.php';
require_once '../core/session_check.php';

$id_dentista = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'update_profile':
            $foto_perfil_nome = null;

            // Lógica de upload de imagem
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == 0) {
                $upload_dir = '../assets/uploads/';
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5 MB

                if (in_array($_FILES['foto_perfil']['type'], $allowed_types) && $_FILES['foto_perfil']['size'] <= $max_size) {
                    // Gera um nome único para o arquivo
                    $file_extension = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
                    $foto_perfil_nome = uniqid('dentista_' . $id_dentista . '_') . '.' . $file_extension;
                    
                    if (!move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $upload_dir . $foto_perfil_nome)) {
                        header("Location: ../views/perfil.php?status=upload_error");
                        exit();
                    }
                } else {
                    header("Location: ../views/perfil.php?status=invalid_file");
                    exit();
                }
            }
            
            // Lógica de atualização dos dados do formulário
            $nome = htmlspecialchars(strip_tags($_POST['nome']));
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $cro = htmlspecialchars(strip_tags($_POST['cro']));

            if (!$email) {
                header("Location: ../views/perfil.php?status=error_email");
                exit();
            }

            // Prepara a query SQL condicionalmente
            if ($foto_perfil_nome !== null) {
                // Se uma nova foto foi enviada, atualiza o campo foto_perfil
                $stmt = $conn->prepare("UPDATE dentistas SET nome = ?, email = ?, cro = ?, foto_perfil = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $nome, $email, $cro, $foto_perfil_nome, $id_dentista);
            } else {
                // Se nenhuma foto foi enviada, não altera o campo foto_perfil
                $stmt = $conn->prepare("UPDATE dentistas SET nome = ?, email = ?, cro = ? WHERE id = ?");
                $stmt->bind_param("sssi", $nome, $email, $cro, $id_dentista);
            }

            if ($stmt->execute()) {
                $_SESSION['user_name'] = $nome;
                if ($foto_perfil_nome !== null) {
                    $_SESSION['user_photo'] = $foto_perfil_nome;
                }
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

            if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_nova_senha)) {
                header("Location: ../views/perfil.php?status=empty_fields");
                exit();
            }
            if ($nova_senha !== $confirmar_nova_senha) {
                header("Location: ../views/perfil.php?status=password_mismatch");
                exit();
            }

            $stmt = $conn->prepare("SELECT senha FROM dentistas WHERE id = ?");
            $stmt->bind_param("i", $id_dentista);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user && password_verify($senha_atual, $user['senha'])) {
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE dentistas SET senha = ? WHERE id = ?");
                $update_stmt->bind_param("si", $nova_senha_hash, $id_dentista);
                $update_stmt->execute();
                header("Location: ../views/perfil.php?status=password_updated");
                $update_stmt->close();
            } else {
                header("Location: ../views/perfil.php?status=current_password_wrong");
            }
            $stmt->close();
            break;
    }
}
$conn->close();
exit();
?>