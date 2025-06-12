<?php
// /odontoclinica/create_admin.php

require_once 'core/db_connection.php';

echo '<pre style="font-family: monospace; line-height: 1.6;">';

// --- DEFINA AQUI OS DADOS DO NOVO ADMIN ---
$nome_admin = 'Administrador';
$email_admin = 'admin@email.com';
$senha_admin_texto_puro = 'admin123';
// -----------------------------------------

echo "Iniciando script de criação de admin...\n";

// 1. Criar o hash da senha de forma segura
$hash_senha = password_hash($senha_admin_texto_puro, PASSWORD_DEFAULT);
echo "Senha em texto puro: " . htmlspecialchars($senha_admin_texto_puro) . "\n";
echo "Hash gerado pela função password_hash(): " . htmlspecialchars($hash_senha) . "\n\n";

// 2. Tentar remover qualquer usuário antigo com o mesmo e-mail para evitar conflitos
$stmt_delete = $conn->prepare("DELETE FROM dentistas WHERE email = ?");
$stmt_delete->bind_param("s", $email_admin);
if ($stmt_delete->execute()) {
    echo "OK - Usuário antigo com o e-mail " . htmlspecialchars($email_admin) . " removido (se existia).\n";
}
$stmt_delete->close();

// 3. Inserir o novo usuário com os dados limpos e o hash recém-criado
$stmt_insert = $conn->prepare("INSERT INTO dentistas (nome, email, senha) VALUES (?, ?, ?)");
$stmt_insert->bind_param("sss", $nome_admin, $email_admin, $hash_senha);

if ($stmt_insert->execute()) {
    echo "<strong>SUCESSO!</strong> Novo administrador criado.\n\n";
    echo "<strong>Use os seguintes dados para fazer login:</strong>\n";
    echo "<strong>E-mail:</strong> " . htmlspecialchars($email_admin) . "\n";
    echo "<strong>Senha:</strong> " . htmlspecialchars($senha_admin_texto_puro) . "\n";
} else {
    echo "<strong>ERRO!</strong> Não foi possível criar o novo administrador. Erro: " . $stmt_insert->error;
}

$stmt_insert->close();
$conn->close();

echo "\nScript finalizado.";
echo '</pre>';

?>