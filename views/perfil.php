<?php
require_once '../core/session_check.php';
require_once '../core/db_connection.php';

// Buscar dados atuais do dentista
$id_dentista = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nome, email, cro FROM dentistas WHERE id = ?");
$stmt->bind_param("i", $id_dentista);
$stmt->execute();
$dentista = $stmt->get_result()->fetch_assoc();

// Mapeamento de mensagens de status para o usuário
$messages = [
    'profile_updated' => ['type' => 'success', 'text' => 'Perfil atualizado com sucesso!'],
    'password_updated' => ['type' => 'success', 'text' => 'Senha alterada com sucesso!'],
    'error' => ['type' => 'danger', 'text' => 'Ocorreu um erro ao processar sua requisição.'],
    'error_email' => ['type' => 'danger', 'text' => 'O formato do e-mail é inválido.'],
    'empty_fields' => ['type' => 'danger', 'text' => 'Todos os campos de senha são obrigatórios.'],
    'password_mismatch' => ['type' => 'danger', 'text' => 'A nova senha e a confirmação não correspondem.'],
    'password_short' => ['type' => 'danger', 'text' => 'A nova senha deve ter pelo menos 6 caracteres.'],
    'current_password_wrong' => ['type' => 'danger', 'text' => 'A senha atual está incorreta.']
];
$status_message = isset($_GET['status']) && isset($messages[$_GET['status']]) ? $messages[$_GET['status']] : null;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - OdontoClínica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="main-wrapper">
        <?php include 'partials/sidebar.php'; ?>
        <main class="main-content">
            <header class="main-header-content">
                <h1>Meu Perfil</h1>
                <p>Gerencie seus dados e sua senha de acesso.</p>
            </header>

            <?php if ($status_message): ?>
                <div class="alert alert-<?php echo $status_message['type']; ?>">
                    <?php echo $status_message['text']; ?>
                </div>
            <?php endif; ?>

            <div class="content-section" style="margin-bottom: 30px;">
                <h2>Alterar Dados Pessoais</h2>
                <form action="../controllers/dentista_controller.php" method="POST" class="form-container">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($dentista['nome']); ?>" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group-half">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($dentista['email']); ?>" required>
                        </div>
                        <div class="form-group-half">
                            <label for="cro">CRO (Conselho Regional)</label>
                            <input type="text" id="cro" name="cro" value="<?php echo htmlspecialchars($dentista['cro']); ?>">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Salvar Dados</button>
                    </div>
                </form>
            </div>

            <div class="content-section">
                <h2>Alterar Senha</h2>
                <form action="../controllers/dentista_controller.php" method="POST" class="form-container">
                     <input type="hidden" name="action" value="update_password">
                    <div class="form-group">
                        <label for="senha_atual">Senha Atual</label>
                        <input type="password" id="senha_atual" name="senha_atual" required>
                    </div>
                    <div class="form-group">
                        <label for="nova_senha">Nova Senha</label>
                        <input type="password" id="nova_senha" name="nova_senha" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmar_nova_senha">Confirmar Nova Senha</label>
                        <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Alterar Senha</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>