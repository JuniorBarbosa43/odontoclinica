<?php
require_once '../core/session_check.php';
require_once '../core/db_connection.php';

$id_dentista = $_SESSION['user_id'];

// Card 1: Total de Pacientes
$result_pacientes = $conn->query("SELECT COUNT(id) as total FROM pacientes");
$total_pacientes = $result_pacientes->fetch_assoc()['total'];

// Card 2: Consultas Agendadas para Hoje
$stmt_hoje = $conn->prepare("SELECT COUNT(id) as total FROM consultas WHERE id_dentista = ? AND DATE(data_consulta) = CURDATE()");
$stmt_hoje->bind_param("i", $id_dentista);
$stmt_hoje->execute();
$consultas_hoje = $stmt_hoje->get_result()->fetch_assoc()['total'];
$stmt_hoje->close();

// Card 3: Faturamento da Semana (Consultas com status 'Realizada')
$stmt_semana = $conn->prepare(
    "SELECT COALESCE(SUM(valor), 0) as total 
     FROM consultas 
     WHERE id_dentista = ? AND YEARWEEK(data_consulta, 1) = YEARWEEK(CURDATE(), 1) AND status = 'Realizada'"
);
$stmt_semana->bind_param("i", $id_dentista);
$stmt_semana->execute();
$faturamento_semana = $stmt_semana->get_result()->fetch_assoc()['total'];
$stmt_semana->close();

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
                <p>Resumo geral da sua clínica.</p>
            </header>

            <div class="dashboard-cards">
                <a href="pacientes.php" class="card-link">
                    <div class="card">
                        <div class="card-icon icon-patients"></div>
                        <div class="card-info">
                            <p>Total de Pacientes</p>
                            <h3><?php echo $total_pacientes; ?></h3>
                        </div>
                    </div>
                </a>
                
                <a href="agenda.php" class="card-link">
                    <div class="card">
                        <div class="card-icon icon-appointments"></div>
                        <div class="card-info">
                            <p>Consultas Hoje</p>
                            <h3><?php echo $consultas_hoje; ?></h3>
                        </div>
                    </div>
                </a>
                
                <a href="#" class="card-link">
                    <div class="card">
                        <div class="card-icon icon-revenue"></div>
                        <div class="card-info">
                            <p>Faturamento da Semana</p>
                            <h3>R$ <?php echo number_format($faturamento_semana, 2, ',', '.'); ?></h3>
                        </div>
                    </div>
                </a>
            </div>

            <div class="content-section">
                <h2>Próximas Atividades</h2>
                <p>Abaixo você pode ver sua agenda visual. Para ver a lista completa de consultas, <a href="agenda.php">clique aqui</a>.</p>
                </div>
        </main>
    </div>
</body>
</html>