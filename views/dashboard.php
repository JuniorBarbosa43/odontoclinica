<?php
require_once '../core/session_check.php';
require_once '../core/db_connection.php';

$id_dentista = $_SESSION['user_id'];

// --- DADOS DOS CARDS ---
// Total de Pacientes
$result_pacientes = $conn->query("SELECT COUNT(id) as total FROM pacientes");
$total_pacientes = $result_pacientes->fetch_assoc()['total'];

// Consultas Agendadas para Hoje
$stmt_hoje = $conn->prepare("SELECT COUNT(id) as total FROM consultas WHERE id_dentista = ? AND DATE(data_consulta) = CURDATE() AND status = 'Agendada'");
$stmt_hoje->bind_param("i", $id_dentista);
$stmt_hoje->execute();
$consultas_hoje = $stmt_hoje->get_result()->fetch_assoc()['total'];
$stmt_hoje->close();

// Faturamento da Semana (Consultas 'Realizada')
$stmt_semana = $conn->prepare(
    "SELECT COALESCE(SUM(valor), 0) as total 
     FROM consultas 
     WHERE id_dentista = ? AND YEARWEEK(data_consulta, 1) = YEARWEEK(CURDATE(), 1) AND status = 'Realizada'"
);
$stmt_semana->bind_param("i", $id_dentista);
$stmt_semana->execute();
$faturamento_semana = $stmt_semana->get_result()->fetch_assoc()['total'];
$stmt_semana->close();


// --- NOVOS DADOS DAS NOTIFICAÇÕES ---

// 1. Aniversariantes do Dia
$stmt_aniversario = $conn->prepare("SELECT nome FROM pacientes WHERE DAY(data_nascimento) = DAY(CURDATE()) AND MONTH(data_nascimento) = MONTH(CURDATE())");
$stmt_aniversario->execute();
$aniversariantes = $stmt_aniversario->get_result();
$stmt_aniversario->close();

// 2. Consultas para o Próximo Dia Útil
// (Esta query é um pouco mais complexa, ela tenta encontrar o próximo dia que não seja domingo)
$stmt_amanha = $conn->prepare(
    "SELECT COUNT(id) as total 
     FROM consultas 
     WHERE id_dentista = ? AND status = 'Agendada' AND 
           DATE(data_consulta) = (
               SELECT CASE DAYOFWEEK(CURDATE())
                   WHEN 6 THEN CURDATE() + INTERVAL 2 DAY -- Se hoje for Sexta, próximo dia útil é Segunda
                   WHEN 7 THEN CURDATE() + INTERVAL 2 DAY -- Se hoje for Sábado, próximo dia útil é Segunda
                   ELSE CURDATE() + INTERVAL 1 DAY
               END
           )"
);
$stmt_amanha->bind_param("i", $id_dentista);
$stmt_amanha->execute();
$consultas_amanha = $stmt_amanha->get_result()->fetch_assoc()['total'];
$stmt_amanha->close();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - OdontoClínica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include 'partials/sidebar.php'; ?>
        <main class="main-content">
            <header class="main-header-content">
                <h1>Dashboard</h1>
                <p>Resumo geral e notificações importantes da sua clínica.</p>
            </header>

            <div class="dashboard-cards">
                </div>

            <div class="notifications-section">
                <h2>Notificações</h2>
                <div class="notifications-container">
                    
                    <div class="notification-item">
                        <div class="notification-icon icon-appointments">🗓️</div>
                        <div class="notification-content">
                            <h4>Próximas Consultas</h4>
                            <?php if ($consultas_amanha > 0): ?>
                                <p>Você tem <strong><?php echo $consultas_amanha; ?></strong> consulta(s) agendada(s) para o próximo dia útil.</p>
                            <?php else: ?>
                                <p>Nenhuma consulta agendada para o próximo dia útil.</p>
                            <?php endif; ?>
                            <a href="agenda.php">Ver agenda</a>
                        </div>
                    </div>

                    <div class="notification-item">
                        <div class="notification-icon icon-birthday">🎂</div>
                        <div class="notification-content">
                            <h4>Aniversariantes de Hoje</h4>
                            <?php if ($aniversariantes->num_rows > 0): ?>
                                <p>Parabéns para: 
                                    <strong>
                                    <?php 
                                        $nomes = [];
                                        while($aniversariante = $aniversariantes->fetch_assoc()) {
                                            $nomes[] = $aniversariante['nome'];
                                        }
                                        echo implode(', ', $nomes);
                                    ?>
                                    </strong>!
                                </p>
                            <?php else: ?>
                                <p>Nenhum paciente faz aniversário hoje.</p>
                            <?php endif; ?>
                            <a href="pacientes.php">Ver pacientes</a>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>