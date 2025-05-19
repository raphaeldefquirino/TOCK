<?php
// Página de redirecionamento para a página de login
// Este arquivo fica na raiz e redireciona para a view de login

// Incluir arquivo de inicialização
require_once 'init.php';

// Redirecionar para a página de login
header("Location: " . Config::$LOGIN_URL);
exit;
