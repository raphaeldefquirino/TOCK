<?php
/**
 * Classe Database
 * Responsável pela conexão e operações com o banco de dados
 */
class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $conn;
    
    /**
     * Construtor com parâmetros de conexão
     * 
     * @param string $host     Host do banco de dados
     * @param string $dbname   Nome do banco de dados
     * @param string $username Usuário do banco de dados
     * @param string $password Senha do banco de dados
     */
    public function __construct($host, $dbname, $username, $password) {
        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Método para conectar ao banco
     * 
     * @return PDO Objeto de conexão PDO
     */
    public function connect() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname};charset=utf8", 
                                  $this->username, 
                                  $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $this->conn;
        } catch(PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }
    
    /**
     * Método para obter a conexão
     * 
     * @return PDO Objeto de conexão PDO
     */
    public function getConnection() {
        return $this->conn;
    }
}
