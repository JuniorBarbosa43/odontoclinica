<?php
// views/partials/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h2 class="sidebar-title">Odonto</h2>
        <span class="sidebar-subtitle">Clínica</span>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="icon-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="pacientes.php" class="<?php echo ($current_page == 'pacientes.php' || $current_page == 'paciente_form.php') ? 'active' : ''; ?>">
                    <i class="icon-patients"></i>
                    <span>Pacientes</span>
                </a>
            </li>
            </ul>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
        </div>
        <a href="../auth/logout.php" class="btn-logout">
            <i class="icon-logout"></i>
            <span>Sair</span>
        </a>
    </div>
</aside>
// views/partials/sidebar.php

// ... (código existente) ...
            <li>
                <a href="pacientes.php" class="<?php echo ($current_page == 'pacientes.php' || $current_page == 'paciente_form.php' || $current_page == 'paciente_detalhes.php') ? 'active' : ''; ?>">
                    <i class="icon-patients"></i>
                    <span>Pacientes</span>
                </a>
            </li>
            
            <li>
                <a href="agenda.php" class="<?php echo ($current_page == 'agenda.php' || $current_page == 'consulta_form.php') ? 'active' : ''; ?>">
                    <i class="icon-calendar"></i>
                    <span>Agenda</span>
                </a>
            </li>
            
        </ul>
    </nav>
// ... (código existente) ...