<?php
// auth/logout.php

session_start(); // Inicia a sessão para poder destruí-la
session_unset(); // Remove todas as variáveis de sessão
session_destroy(); // Destrói a sessão

// Redireciona para a página de login
header("Location: ../views/login.php?logout=1");
exit();
?>