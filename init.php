<?php
// Arquivo de inicialização do sistema
// Carrega as classes necessárias e configura o autoload

// Definir o caminho base do projeto usando realpath para garantir caminho absoluto
define('BASE_PATH', realpath(dirname(__FILE__)));

// Função de autoload para carregar classes automaticamente
spl_autoload_register(function($className) {
    // Converter nome da classe para minúsculo para compatibilidade com os arquivos existentes
    $classNameLower = strtolower($className);
    
    // Verificar primeiro na pasta classes
    $classFile = BASE_PATH . '/classes/' . $classNameLower . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
        return;
    }
    
    // Verificar na pasta config
    $configFile = BASE_PATH . '/config/' . $classNameLower . '.php';
    if (file_exists($configFile)) {
        require_once $configFile;
        return;
    }
});

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
