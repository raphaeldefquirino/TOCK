<?php

/**
 * Classe User
 * Responsável por gerenciar os dados e operações relacionadas aos usuários/funcionários
 */
class User
{
    private $conn;
    private $table = 'usuarios';

    // Propriedades do usuário/funcionário
    public $id;
    public $nome;
    public $email;
    public $senha;
    public $cargo;
    public $departamento;
    public $nivel_acesso;
    public $telefone;
    public $data_admissao;
    public $status;
    public $observacoes;
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
     * @return array|false Retorna os dados do usuário ou false se não encontrado
     */
    public function findByEmail($email)
    {
        // Inclui todos os campos da tabela usuarios na consulta
        $query = "SELECT id, nome, email, senha, cargo, departamento, nivel_acesso, telefone, data_admissao, status, observacoes, data_criacao 
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
     * Método para criar novo usuário/funcionário
     * 
     * @return boolean|string Retorna true em sucesso, ou uma string com a mensagem de erro em caso de falha.
     */
    public function create()
    {
        // Query inclui todos os novos campos
        $query = "INSERT INTO {$this->table} 
                  (nome, email, senha, cargo, departamento, nivel_acesso, telefone, data_admissao, status, observacoes, data_criacao) 
                  VALUES (:nome, :email, :senha, :cargo, :departamento, :nivel_acesso, :telefone, :data_admissao, :status, :observacoes, NOW())";

        try {
            $stmt = $this->conn->prepare($query);

            // Limpar e sanitizar dados
            $this->nome = htmlspecialchars(strip_tags($this->nome));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->cargo = htmlspecialchars(strip_tags($this->cargo));
            $this->departamento = htmlspecialchars(strip_tags($this->departamento));
            $this->nivel_acesso = htmlspecialchars(strip_tags($this->nivel_acesso));
            $this->telefone = !empty($this->telefone) ? htmlspecialchars(strip_tags($this->telefone)) : null; // Permitir nulo
            $this->data_admissao = htmlspecialchars(strip_tags($this->data_admissao)); 
            $this->status = htmlspecialchars(strip_tags($this->status)); 
            $this->observacoes = !empty($this->observacoes) ? htmlspecialchars(strip_tags($this->observacoes)) : null; // Permitir nulo

            // Hash da senha
            $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);

            // Bind dos parâmetros
            $stmt->bindParam(':nome', $this->nome);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':senha', $this->senha);
            $stmt->bindParam(':cargo', $this->cargo);
            $stmt->bindParam(':departamento', $this->departamento);
            $stmt->bindParam(':nivel_acesso', $this->nivel_acesso);
            $stmt->bindParam(':telefone', $this->telefone);
            $stmt->bindParam(':data_admissao', $this->data_admissao);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':observacoes', $this->observacoes);

            // Executar query
            if ($stmt->execute()) {
                return true;
            } else {
                // Retorna a mensagem de erro do PDO em caso de falha na execução
                $errorInfo = $stmt->errorInfo();
                return "Erro PDO [{$errorInfo[0]}][{$errorInfo[1]}]: {$errorInfo[2]}";
            }
        } catch (PDOException $e) {
            // Captura exceções gerais do PDO (ex: erro de conexão, preparação)
            return "Exceção PDO: " . $e->getMessage();
        }
    }

    /**
     * Método para atualizar usuário/funcionário
     * 
     * @return boolean Resultado da operação
     */
    public function update()
    {
        // Query inclui todos os campos atualizáveis (exceto senha e data_criacao)
        $query = "UPDATE {$this->table} 
                  SET nome = :nome, 
                      email = :email, 
                      cargo = :cargo,
                      departamento = :departamento,
                      nivel_acesso = :nivel_acesso,
                      telefone = :telefone,
                      data_admissao = :data_admissao,
                      status = :status,
                      observacoes = :observacoes
                  WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);

            // Limpar e sanitizar dados
            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->nome = htmlspecialchars(strip_tags($this->nome));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->cargo = htmlspecialchars(strip_tags($this->cargo));
            $this->departamento = htmlspecialchars(strip_tags($this->departamento));
            $this->nivel_acesso = htmlspecialchars(strip_tags($this->nivel_acesso));
            $this->telefone = !empty($this->telefone) ? htmlspecialchars(strip_tags($this->telefone)) : null;
            $this->data_admissao = htmlspecialchars(strip_tags($this->data_admissao));
            $this->status = htmlspecialchars(strip_tags($this->status));
            $this->observacoes = !empty($this->observacoes) ? htmlspecialchars(strip_tags($this->observacoes)) : null;

            // Bind dos parâmetros
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':nome', $this->nome);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':cargo', $this->cargo);
            $stmt->bindParam(':departamento', $this->departamento);
            $stmt->bindParam(':nivel_acesso', $this->nivel_acesso);
            $stmt->bindParam(':telefone', $this->telefone);
            $stmt->bindParam(':data_admissao', $this->data_admissao);
            $stmt->bindParam(':status', $this->status);
            $stmt->bindParam(':observacoes', $this->observacoes);

            // Executar query
            if ($stmt->execute()) {
                return true;
            }
            return false; // Ou retornar mensagem de erro como no create()
        } catch (PDOException $e) {
            // Logar ou tratar o erro
            return false;
        }
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
        try {
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
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Método para verificar se um email já existe (exceto para um ID específico)
     * 
     * @param string $email Email a ser verificado
     * @param int|null $excludeId ID do usuário a ser excluído da verificação (útil na atualização)
     * @return boolean True se o email já existe, False caso contrário
     */
    public function emailExists($email, $excludeId = null)
    {
        $query = "SELECT id FROM {$this->table} WHERE email = :email";
        if ($excludeId !== null) {
            $query .= " AND id != :excludeId";
        }
        $query .= " LIMIT 1";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            if ($excludeId !== null) {
                $stmt->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
            }
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            // Em caso de erro na consulta, pode ser prudente retornar true para evitar duplicidade
            // ou logar o erro e retornar false/lançar exceção dependendo da política de erro.
            error_log("Erro ao verificar existência de email: " . $e->getMessage());
            return false; // Ou true, dependendo da abordagem de segurança
        }
    }
}

