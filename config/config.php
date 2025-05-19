<?php
/**
 * Classe Config
 * Responsável por gerenciar configurações globais do sistema
 */
class Config {
    // Configurações do banco de dados
    public static $DB_HOST = 'localhost';
    public static $DB_NAME = 'estoque_db';
    public static $DB_USER = 'root';
    public static $DB_PASS = '';
    
    // URLs do sistema
    public static $BASE_URL = '/TOCK';
    public static $LOGIN_URL = '/TOCK/views/auth/login.php';
    public static $CADASTRO_URL = '/TOCK/views/auth/cadastro.php';
    
    // Outras configurações
    public static $SESSION_TIMEOUT = 1800; // 30 minutos
}
