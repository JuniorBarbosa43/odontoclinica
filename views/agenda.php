<?php
require_once '../core/session_check.php';
require_once '../core/db_connection.php';

// Busca as próximas consultas (de hoje em diante) para a tabela de lista
$id_dentista = $_SESSION['user_id'];
$consultas_result = $conn->prepare(
    "SELECT c.id, c.data_consulta, c.procedimento, c.status, p.nome as nome_paciente 
     FROM consultas c 
     JOIN pacientes p ON c.id_paciente = p.id 
     WHERE c.id_dentista = ? AND c.data_consulta >= CURDATE()
     ORDER BY c.data_consulta ASC"
);
$consultas_result->bind_param("i", $id_dentista);
$consultas_result->execute();
$consultas = $consultas_result->get_result();
// ... (dentro do arquivo views/agenda.php, no início do bloco de alertas) ...

<?php if(isset($_GET['status'])): ?>
    <div class="alert alert-<?php echo in_array($_GET['status'], ['error', 'error_fk', 'reminder_failed']) ? 'danger' : 'success'; ?>">
        <?php
            switch ($_GET['status']) {
                case 'created': echo 'Consulta agendada com sucesso!'; break;
                case 'updated': echo 'Consulta atualizada com sucesso!'; break;
                case 'deleted': echo 'Consulta removida com sucesso!'; break;
                // NOVAS MENSAGENS
                case 'reminder_sent': echo 'Lembrete de consulta enviado por e-mail com sucesso!'; break;
                case 'reminder_failed': echo 'O lembrete não pôde ser enviado. Verifique a configuração do servidor de e-mail.'; break;
                // ---
                case 'error_fk': echo 'Erro: Não é possível remover pacientes com consultas agendadas.'; break;
                default: echo 'Ocorreu um erro ao processar a sua requisição.'; break;
            }
        ?>
    </div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - OdontoClínica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include 'partials/sidebar.php'; ?>
        <main class="main-content">
            <header class="main-header-content">
                <div class="header-content-left">
                    <h1>Agenda</h1>
                    <p>Visualize e gerencie suas consultas.</p>
                </div>
                <div class="header-content-right">
                    <a href="consulta_form.php?action=add" class="btn btn-primary">Nova Consulta</a>
                </div>
            </header>
            
            <div class="content-section" style="margin-bottom: 30px;">
                <div class="agenda-controls">
                    <button id="prev-week" class="btn btn-secondary">&lt; Semana Anterior</button>
                    <h2 id="current-week-display"></h2>
                    <button id="next-week" class="btn btn-secondary">Próxima Semana &gt;</button>
                </div>
                <div id="agenda-visual">
                    </div>
            </div>

            <div class="content-section">
                <h2>Próximas Consultas (Lista)</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Data e Hora</th>
                            <th>Paciente</th>
                            <th>Procedimento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($consultas->num_rows > 0): ?>
                            <?php while($consulta = $consultas->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo (new DateTime($consulta['data_consulta']))->format('d/m/Y H:i'); ?></td>
                                    <td><?php echo htmlspecialchars($consulta['nome_paciente']); ?></td>
                                    <td><?php echo htmlspecialchars($consulta['procedimento']); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($consulta['status']); ?>"><?php echo htmlspecialchars($consulta['status']); ?></span></td>
<td class="actions-cell">
    <a href="consulta_form.php?action=edit&id=<?php echo $consulta['id']; ?>" class="btn btn-sm btn-warning" title="Editar">✏️</a>
    
    <a href="../controllers/lembrete_controller.php?id=<?php echo $consulta['id']; ?>" class="btn btn-sm btn-info" title="Enviar Lembrete por E-mail" onclick="return confirm('Deseja enviar um lembrete por e-mail para este paciente?');">✉️</a>
    
    <form action="../controllers/consulta_controller.php" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta consulta?');">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id_consulta" value="<?php echo $consulta['id']; ?>">
        <button type="submit" class="btn btn-sm btn-danger" title="Excluir">🗑️</button>
    </form>
</td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">Nenhuma consulta agendada para o futuro.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="../assets/js/agenda.js"></script>
</body>
</html>