<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OdontoClínica</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-body">

    <div class="login-container">
        <div class="login-header">
            <h2>OdontoClínica</h2>
            <p>Acesse sua conta para gerenciar sua clínica</p>
        </div>

        <?php if(isset($_GET['error'])): ?>
            <div class="error-message">E-mail ou senha incorretos.</div>
        <?php endif; ?>
        <?php if(isset($_GET['logout'])): ?>
            <div class="success-message">Você saiu com segurança.</div>
        <?php endif; ?>
         <?php if(isset($_GET['auth_required'])): ?>
            <div class="error-message">Você precisa fazer login para acessar esta página.</div>
        <?php endif; ?>

        <form action="../auth/login_process.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
        <div class="login-footer">
            <a href="#">Esqueceu sua senha?</a>
        </div>
    </div>

</body>
</html>