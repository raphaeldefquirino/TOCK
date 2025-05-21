<?php

/**
 * Classe User
 * Responsável por gerenciar os dados e operações relacionadas aos usuários
 */
class User
{
    private $conn;
    private $table = 'usuarios';

    // Propriedades do usuário
    public $id;
    public $nome;
    public $email;
    public $senha;
    public $data_criacao;

    /**
     * Construtor recebe a conexão
     * 
     * @param PDO $db Conexão com o banco de dados
     */
    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Método para buscar usuário por email
     * 
     * @param string $email Email do usuário
     * @return PDOStatement Resultado da consulta
     */

    public function findByEmail($email)
    {
        $query = "SELECT id, nome, email, senha, data_criacao 
              FROM {$this->table} 
              WHERE email = :email 
              LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Retornar os dados diretamente
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Método para criar novo usuário
     * 
     * @return boolean Resultado da operação
     */
    public function create()
    {
        $query = "INSERT INTO {$this->table} 
                  (nome, email, senha, data_criacao) 
                  VALUES (:nome, :email, :senha, NOW())";

        $stmt = $this->conn->prepare($query);

        // Limpar e sanitizar dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));

        // Hash da senha
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

        // Bind dos parâmetros
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':senha', $this->senha);

        // Executar query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Método para atualizar usuário
     * 
     * @return boolean Resultado da operação
     */
    public function update()
    {
        $query = "UPDATE {$this->table} 
                  SET nome = :nome, 
                      email = :email 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpar e sanitizar dados
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind dos parâmetros
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':id', $this->id);

        // Executar query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Método para atualizar senha
     * 
     * @return boolean Resultado da operação
     */
    public function updatePassword()
    {
        $query = "UPDATE {$this->table} 
                  SET senha = :senha 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Hash da nova senha
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind dos parâmetros
        $stmt->bindParam(':senha', $this->senha);
        $stmt->bindParam(':id', $this->id);

        // Executar query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
