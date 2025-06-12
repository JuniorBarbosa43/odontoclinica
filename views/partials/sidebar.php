<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);

// Busca a foto no banco de dados se ela ainda não estiver na sessão.
// Isso evita uma consulta ao banco em cada carregamento de página.
if (!isset($_SESSION['user_photo'])) {
    require_once __DIR__ . '/../../core/db_connection.php';
    if(isset($_SESSION['user_id'])) {
        $stmt_photo = $conn->prepare("SELECT foto_perfil FROM dentistas WHERE id = ?");
        $stmt_photo->bind_param("i", $_SESSION['user_id']);
        $stmt_photo->execute();
        $result_photo = $stmt_photo->get_result()->fetch_assoc();
        $_SESSION['user_photo'] = $result_photo['foto_perfil'] ?? 'default.png';
        $stmt_photo->close();
    }
}
$user_photo = $_SESSION['user_photo'] ?? 'default.png';
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <img src="../assets/uploads/<?php echo htmlspecialchars($user_photo); ?>" alt="Foto de Perfil" class="sidebar-profile-pic">
        <h2 class="sidebar-title">Odonto</h2>
        <span class="sidebar-subtitle">Clínica</span>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="icon-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
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
    <div class="sidebar-footer">
        <div class="user-info">
            <span>Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
            <a href="perfil.php" class="profile-link">Ver Perfil</a>
        </div>
        <a href="../auth/logout.php" class="btn-logout">
            <i class="icon-logout"></i>
            <span>Sair</span>
        </a>
    </div>
</aside>