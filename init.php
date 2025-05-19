<?php
// Arquivo de inicialização do sistema
// Carrega as classes necessárias e configura o autoload

// Definir o caminho base do projeto
define('BASE_PATH', dirname(__DIR__));

// Função de autoload para carregar classes automaticamente
spl_autoload_register(function($className) {
    // Verificar primeiro na pasta classes
    $classFile = BASE_PATH . '/classes/' . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
        return;
    }
    
    // Verificar na pasta config
    $configFile = BASE_PATH . '/config/' . $className . '.php';
    if (file_exists($configFile)) {
        require_once $configFile;
        return;
    }
});

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
