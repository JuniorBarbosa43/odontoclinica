<?php
// core/db_connection.php

// Definições do banco de dados
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Usuário padrão do XAMPP
define('DB_PASSWORD', '');     // Senha padrão do XAMPP
define('DB_NAME', 'odontoclinica_db');

// Cria a conexão
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Checa a conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Define o charset para UTF-8 para evitar problemas com acentuação
$conn->set_charset("utf8mb4");
?>