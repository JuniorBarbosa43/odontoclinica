<?php
require_once '../core/session_check.php';
require_once '../core/db_connection.php';

// --- LÓGICA DE PAGINAÇÃO E BUSCA ---

// 1. Definições da Paginação
$limit = 15; // Itens por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 2. Lógica de Busca
$search_term = $_GET['search'] ?? '';
$params = [];
$types = '';

// 3. Monta a query para CONTAR o total de registros (para calcular as páginas)
$count_sql = "SELECT COUNT(id) FROM pacientes";
if (!empty($search_term)) {
    $count_sql .= " WHERE nome LIKE ? OR cpf LIKE ?";
    $like_term = "%{$search_term}%";
    $params[] = $like_term;
    $params[] = $like_term;
    $types .= 'ss';
}

$stmt_count = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_results = $stmt_count->get_result()->fetch_row()[0];
$total_pages = ceil($total_results / $limit);
$stmt_count->close();

// 4. Monta a query para BUSCAR os registros da página atual
$sql = "SELECT id, nome, cpf, telefone FROM pacientes";
if (!empty($search_term)) {
    // A query de busca é a mesma, só adicionamos a paginação
    $sql .= " WHERE nome LIKE ? OR cpf LIKE ?";
}
$sql .= " ORDER BY nome ASC LIMIT ? OFFSET ?";

// Adiciona os parâmetros de LIMIT e OFFSET
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$pacientes = $stmt->get_result();

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

            <div class="search-container content-section" style="margin-bottom: 20px;">
                <form action="pacientes.php" method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Buscar por nome ou CPF..." value="<?php echo htmlspecialchars($search_term); ?>" class="search-input">
                    <button type="submit" class="btn btn-secondary">Buscar</button>
                    <a href="pacientes.php" class="btn btn-secondary">Limpar</a>
                </form>
            </div>

            <div class="content-section">
                <table class="data-table">
                    <thead> ... </thead>
                    <tbody>
                        <?php if ($pacientes->num_rows > 0): ?>
                            <?php while($paciente = $pacientes->fetch_assoc()): ?>
                                <tr> ... </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr> ... </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div class="pagination-container">
                    <?php if ($total_pages > 1): ?>
                        <nav class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search_term); ?>" class="page-link">Anterior</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_term); ?>" class="page-link <?php if ($i == $page) echo 'active'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search_term); ?>" class="page-link">Próxima</a>
                            <?php endif; ?>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>