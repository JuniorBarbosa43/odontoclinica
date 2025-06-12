<?php
require_once '../core/session_check.php';
require_once '../core/db_connection.php';

$id_paciente = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id_paciente) {
    header("Location: pacientes.php");
    exit();
}

// Buscar dados do paciente
$stmt_paciente = $conn->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt_paciente->bind_param("i", $id_paciente);
$stmt_paciente->execute();
$paciente = $stmt_paciente->get_result()->fetch_assoc();

if (!$paciente) {
    header("Location: pacientes.php?status=notfound");
    exit();
}

// Buscar histórico de consultas do paciente
$id_dentista = $_SESSION['user_id'];
$stmt_consultas = $conn->prepare(
    "SELECT * FROM consultas WHERE id_paciente = ? AND id_dentista = ? ORDER BY data_consulta DESC"
);
$stmt_consultas->bind_param("ii", $id_paciente, $id_dentista);
$stmt_consultas->execute();
$consultas = $stmt_consultas->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes de <?php echo htmlspecialchars($paciente['nome']); ?> - OdontoClínica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include 'partials/sidebar.php'; ?>
        <main class="main-content">
            <header class="main-header-content">
                <h1><?php echo htmlspecialchars($paciente['nome']); ?></h1>
                <a href="pacientes.php" class="btn btn-secondary">Voltar para Pacientes</a>
            </header>

            <div class="content-section" style="margin-bottom: 30px;">
                <h2>Dados do Paciente</h2>
                <p><strong>CPF:</strong> <?php echo htmlspecialchars($paciente['cpf']); ?></p>
                <p><strong>Data de Nascimento:</strong> <?php echo (new DateTime($paciente['data_nascimento']))->format('d/m/Y'); ?></p>
                <p><strong>Telefone:</strong> <?php echo htmlspecialchars($paciente['telefone']); ?></p>
                <p><strong>Observações:</strong> <?php echo nl2br(htmlspecialchars($paciente['observacoes'])); ?></p>
            </div>

            <div class="content-section">
                <h2>Histórico de Consultas</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Procedimento</th>
                            <th>Valor (R$)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($consultas->num_rows > 0): ?>
                            <?php while($consulta = $consultas->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo (new DateTime($consulta['data_consulta']))->format('d/m/Y H:i'); ?></td>
                                    <td><?php echo htmlspecialchars($consulta['procedimento']); ?></td>
                                    <td><?php echo number_format($consulta['valor'], 2, ',', '.'); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($consulta['status']); ?>"><?php echo htmlspecialchars($consulta['status']); ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Nenhuma consulta encontrada para este paciente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>