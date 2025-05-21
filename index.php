<?php
// Arquivo de redirecionamento para a página de login
// Redireciona o usuário para a página de login quando acessa a raiz do projeto

// Incluir arquivo de inicialização
require_once 'init.php';

// Redirecionar para a página de login
header("Location: " . Config::$LOGIN_URL);
exit;
