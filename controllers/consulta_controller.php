<?php
// controllers/consulta_controller.php
session_start();
require_once '../core/db_connection.php';
require_once '../core/session_check.php';

// Apenas dentistas logados podem acessar
$id_dentista = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    // Sanitização dos dados do formulário
    $id_paciente = filter_input(INPUT_POST, 'id_paciente', FILTER_SANITIZE_NUMBER_INT);
    $data = $_POST['data_consulta'] ?? '';
    $hora = $_POST['hora_consulta'] ?? '';
    $data_consulta = $data . ' ' . $hora; // Concatena data e hora
    $procedimento = htmlspecialchars(strip_tags($_POST['procedimento'] ?? ''));
    $valor = filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $observacoes = htmlspecialchars(strip_tags($_POST['observacoes'] ?? ''));
    $status = htmlspecialchars(strip_tags($_POST['status'] ?? 'Agendada'));
    $id_consulta = filter_input(INPUT_POST, 'id_consulta', FILTER_SANITIZE_NUMBER_INT);

    switch ($action) {
        case 'add':
            $stmt = $conn->prepare("INSERT INTO consultas (id_paciente, id_dentista, data_consulta, procedimento, valor, observacoes, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iisssss", $id_paciente, $id_dentista, $data_consulta, $procedimento, $valor, $observacoes, $status);
            
            if ($stmt->execute()) {
                header("Location: ../views/agenda.php?status=created");
            } else {
                header("Location: ../views/agenda.php?status=error");
            }
            $stmt->close();
            break;

        case 'edit':
            $stmt = $conn->prepare("UPDATE consultas SET id_paciente = ?, data_consulta = ?, procedimento = ?, valor = ?, observacoes = ?, status = ? WHERE id = ? AND id_dentista = ?");
            $stmt->bind_param("isssssii", $id_paciente, $data_consulta, $procedimento, $valor, $observacoes, $status, $id_consulta, $id_dentista);

            if ($stmt->execute()) {
                header("Location: ../views/agenda.php?status=updated");
            } else {
                header("Location: ../views/agenda.php?status=error");
            }
            $stmt->close();
            break;

        case 'delete':
            $stmt = $conn->prepare("DELETE FROM consultas WHERE id = ? AND id_dentista = ?");
            $stmt->bind_param("ii", $id_consulta, $id_dentista);
            
            if ($stmt->execute()) {
                header("Location: ../views/agenda.php?status=deleted");
            } else {
                header("Location: ../views/agenda.php?status=error");
            }
            $stmt->close();
            break;
    }
}

$conn->close();
exit();
?>