<?php
require_once '../core/session_check.php';
require_once '../core/db_connection.php';

$pacientes = $conn->query("SELECT id, nome, cpf, telefone FROM pacientes ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes - OdontoClínica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include 'partials/sidebar.php'; ?>
        <main class="main-content">
            <header class="main-header-content">
                <div class="header-content-left">
                    <h1>Pacientes</h1>
                    <p>Gerencie os pacientes da clínica.</p>
                </div>
                <div class="header-content-right">
                    <a href="paciente_form.php?action=add" class="btn btn-primary">Adicionar Paciente</a>
                </div>
            </header>

            <?php if(isset($_GET['status'])): ?>
                <div class="alert alert-<?php echo $_GET['status'] == 'error' || $_GET['status'] == 'error_fk' ? 'danger' : 'success'; ?>">
                    <?php
                        switch ($_GET['status']) {
                            case 'created': echo 'Paciente adicionado com sucesso!'; break;
                            case 'updated': echo 'Paciente atualizado com sucesso!'; break;
                            case 'deleted': echo 'Paciente removido com sucesso!'; break;
                            case 'error_fk': echo 'Erro: Não é possível remover pacientes com consultas agendadas.'; break;
                            default: echo 'Ocorreu um erro ao processar a sua requisição.'; break;
                        }
                    ?>
                </div>
            <?php endif; ?>

            <div class="content-section">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>CPF</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($pacientes->num_rows > 0): ?>
                            <?php while($paciente = $pacientes->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($paciente['nome']); ?></td>
                                    <td><?php echo htmlspecialchars($paciente['cpf']); ?></td>
                                    <td><?php echo htmlspecialchars($paciente['telefone']); ?></td>
                                    <td class="actions-cell">
                                        <a href="paciente_form.php?action=edit&id=<?php echo $paciente['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                        <form action="../controllers/paciente_controller.php" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir este paciente? Esta ação não pode ser desfeita.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $paciente['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Nenhum paciente cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>