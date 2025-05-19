<?php
// Página de logout utilizando POO com estrutura reorganizada
// Usando caminho absoluto para o init.php

// Definir caminho absoluto para a raiz do projeto
$rootPath = dirname(dirname(__DIR__));
require_once $rootPath . '/init.php';

// Inicializar objetos
$database = new Database(Config::$DB_HOST, Config::$DB_NAME, Config::$DB_USER, Config::$DB_PASS);
$db = $database->connect();
$user = new User($db);
$auth = new Auth($user);

// Fazer logout
$auth->logout();

// Redirecionar para a página de login
header("Location: " . Config::$LOGIN_URL);
exit;
