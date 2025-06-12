<?php
// controllers/paciente_controller.php
session_start();
require_once '../core/db_connection.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../views/login.php?auth_required=1");
    exit();
}

// Verifica se a requisição é do tipo POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // Sanitiza os inputs (exemplo simples)
    $nome = htmlspecialchars(strip_tags($_POST['nome'] ?? ''));
    $cpf = htmlspecialchars(strip_tags($_POST['cpf'] ?? ''));
    $data_nascimento = htmlspecialchars(strip_tags($_POST['data_nascimento'] ?? ''));
    $telefone = htmlspecialchars(strip_tags($_POST['telefone'] ?? ''));
    $observacoes = htmlspecialchars(strip_tags($_POST['observacoes'] ?? ''));
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);


    switch ($action) {
        case 'add':
            $stmt = $conn->prepare("INSERT INTO pacientes (nome, cpf, data_nascimento, telefone, observacoes) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nome, $cpf, $data_nascimento, $telefone, $observacoes);
            
            if ($stmt->execute()) {
                header("Location: ../views/pacientes.php?status=created");
            } else {
                header("Location: ../views/pacientes.php?status=error");
            }
            $stmt->close();
            break;

        case 'edit':
            $stmt = $conn->prepare("UPDATE pacientes SET nome = ?, cpf = ?, data_nascimento = ?, telefone = ?, observacoes = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $nome, $cpf, $data_nascimento, $telefone, $observacoes, $id);

            if ($stmt->execute()) {
                header("Location: ../views/pacientes.php?status=updated");
            } else {
                header("Location: ../views/pacientes.php?status=error");
            }
            $stmt->close();
            break;

        case 'delete':
            // IMPORTANTE: Em um sistema real, considere a exclusão lógica (marcar como inativo)
            // em vez de apagar fisicamente, para manter o histórico de consultas.
            // Por simplicidade do MVP, faremos a exclusão física.
            $stmt = $conn->prepare("DELETE FROM pacientes WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                header("Location: ../views/pacientes.php?status=deleted");
            } else {
                // Erro pode ocorrer se o paciente tiver consultas associadas (Foreign Key)
                header("Location: ../views/pacientes.php?status=error_fk");
            }
            $stmt->close();
            break;
    }
}

$conn->close();
exit();
?>