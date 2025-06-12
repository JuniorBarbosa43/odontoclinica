<?php
require_once '../core/session_check.php';
require_once '../core/db_connection.php';

// Carregar lista de pacientes para o dropdown
$pacientes_result = $conn->query("SELECT id, nome FROM pacientes ORDER BY nome ASC");

$action = $_GET['action'] ?? 'add';
$page_title = $action == 'add' ? 'Agendar Nova Consulta' : 'Editar Consulta';
$consulta = [
    'id' => '', 'id_paciente' => '', 'data_consulta' => date('Y-m-d'), 'procedimento' => '',
    'valor' => '', 'observacoes' => '', 'status' => 'Agendada'
];
$hora_consulta = date('H:i');

if ($action == 'edit') {
    $id_consulta = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($id_consulta) {
        $id_dentista = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT * FROM consultas WHERE id = ? AND id_dentista = ?");
        $stmt->bind_param("ii", $id_consulta, $id_dentista);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $consulta = $result->fetch_assoc();
            // Separa data e hora para os campos do formulário
            $datetime = new DateTime($consulta['data_consulta']);
            $consulta['data_consulta'] = $datetime->format('Y-m-d');
            $hora_consulta = $datetime->format('H:i');
        } else {
            header("Location: agenda.php?status=notfound");
            exit();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - OdontoClínica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include 'partials/sidebar.php'; ?>
        <main class="main-content">
            <header class="main-header-content">
                <h1><?php echo $page_title; ?></h1>
                <p>Preencha os dados da consulta abaixo.</p>
            </header>

            <div class="content-section">
                <form action="../controllers/consulta_controller.php" method="POST" class="form-container">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <input type="hidden" name="id_consulta" value="<?php echo $consulta['id']; ?>">

                    <div class="form-group">
                        <label for="id_paciente">Paciente</label>
                        <select id="id_paciente" name="id_paciente" required>
                            <option value="">Selecione um paciente</option>
                            <?php while($paciente = $pacientes_result->fetch_assoc()): ?>
                                <option value="<?php echo $paciente['id']; ?>" <?php echo ($paciente['id'] == $consulta['id_paciente']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($paciente['nome']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group-half">
                            <label for="data_consulta">Data da Consulta</label>
                            <input type="date" id="data_consulta" name="data_consulta" value="<?php echo htmlspecialchars($consulta['data_consulta']); ?>" required>
                        </div>
                        <div class="form-group-half">
                            <label for="hora_consulta">Hora</label>
                            <input type="time" id="hora_consulta" name="hora_consulta" value="<?php echo htmlspecialchars($hora_consulta); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="procedimento">Procedimento</label>
                        <input type="text" id="procedimento" name="procedimento" value="<?php echo htmlspecialchars($consulta['procedimento']); ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group-half">
                            <label for="valor">Valor (R$)</label>
                            <input type="number" step="0.01" id="valor" name="valor" value="<?php echo htmlspecialchars($consulta['valor']); ?>" required>
                        </div>
                        <div class="form-group-half">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="Agendada" <?php echo ($consulta['status'] == 'Agendada') ? 'selected' : ''; ?>>Agendada</option>
                                <option value="Realizada" <?php echo ($consulta['status'] == 'Realizada') ? 'selected' : ''; ?>>Realizada</option>
                                <option value="Cancelada" <?php echo ($consulta['status'] == 'Cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea id="observacoes" name="observacoes" rows="4"><?php echo htmlspecialchars($consulta['observacoes']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Salvar Consulta</button>
                        <a href="agenda.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>