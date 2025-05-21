<?php
/**
 * Classe Database
 * Responsável pela conexão com o banco de dados
 */
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    
    /**
     * Construtor recebe os dados de conexão
     * 
     * @param string $host Host do banco de dados
     * @param string $db_name Nome do banco de dados
     * @param string $username Usuário do banco de dados
     * @param string $password Senha do banco de dados
     */
    public function __construct($host, $db_name, $username, $password) {
        $this->host = $host;
        $this->db_name = $db_name;
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Método para conectar ao banco de dados
     * 
     * @return PDO Conexão com o banco de dados
     */
    public function connect() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $e) {
            echo "Erro de conexão: " . $e->getMessage();
        }
        
        return $this->conn;
    }
}
