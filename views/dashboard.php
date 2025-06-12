<?php
require_once '../core/session_check.php';
require_once '../core/db_connection.php';

// Busca o número total de pacientes
$result_pacientes = $conn->query("SELECT COUNT(id) as total FROM pacientes");
$total_pacientes = $result_pacientes->fetch_assoc()['total'];

// Futuramente: Buscar o número de consultas do dia/semana
$consultas_hoje = 0; // Placeholder

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
                <div class="card">
                    <div class="card-icon icon-patients"></div>
                    <div class="card-info">
                        <p>Total de Pacientes</p>
                        <h3><?php echo $total_pacientes; ?></h3>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-icon icon-appointments"></div>
                    <div class="card-info">
                        <p>Consultas Hoje</p>
                        <h3><?php echo $consultas_hoje; ?></h3>
                    </div>
                </div>
                
                </div>

            <div class="content-section">
                <h2>Próximas Atividades</h2>
                <p>A agenda interativa e o histórico de atividades aparecerão aqui em breve.</p>
            </div>
        </main>
    </div>
</body>
</html>