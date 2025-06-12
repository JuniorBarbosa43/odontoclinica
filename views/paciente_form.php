<?php
require_once '../core/session_check.php';
require_once '../core/db_connection.php';

$action = $_GET['action'] ?? 'add';
$page_title = $action == 'add' ? 'Adicionar Paciente' : 'Editar Paciente';
$paciente = [
    'id' => '', 'nome' => '', 'cpf' => '', 'data_nascimento' => '',
    'telefone' => '', 'observacoes' => ''
];

if ($action == 'edit') {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if ($id) {
        $stmt = $conn->prepare("SELECT * FROM pacientes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $paciente = $result->fetch_assoc();
        } else {
            // Paciente não encontrado, redirecionar ou mostrar erro
            header("Location: pacientes.php?status=notfound");
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
                <p>Preencha os dados abaixo.</p>
            </header>

            <div class="content-section">
                <form action="../controllers/paciente_controller.php" method="POST" class="form-container">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <input type="hidden" name="id" value="<?php echo $paciente['id']; ?>">

                    <div class="form-row">
                        <div class="form-group-half">
                            <label for="nome">Nome Completo</label>
                            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($paciente['nome']); ?>" required>
                        </div>
                        <div class="form-group-half">
                            <label for="cpf">CPF</label>
                            <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($paciente['cpf']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group-half">
                            <label for="data_nascimento">Data de Nascimento</label>
                            <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($paciente['data_nascimento']); ?>" required>
                        </div>
                        <div class="form-group-half">
                            <label for="telefone">Telefone</label>
                            <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($paciente['telefone']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea id="observacoes" name="observacoes" rows="4"><?php echo htmlspecialchars($paciente['observacoes']); ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <a href="pacientes.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>