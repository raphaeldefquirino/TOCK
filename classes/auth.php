<?php

/**
 * Classe Auth
 * Responsável pela autenticação e gerenciamento de sessões
 */
class Auth
{
    private $user;

    /**
     * Construtor recebe objeto User
     * 
     * @param User $user Objeto da classe User
     */
    public function __construct($user)
    {
        $this->user = $user;

        // Iniciar sessão se não estiver iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Método para autenticar usuário
     * 
     * @param string $email Email do usuário
     * @param string $password Senha do usuário
     * @return boolean Resultado da autenticação
     */
    public function login($email, $password)
    {
        $row = $this->user->findByEmail($email); // Retorna array associativo com todos os campos

        if ($row && password_verify($password, $row["senha"])) {
            // Armazenar todos os dados relevantes do usuário na sessão
            // Isso evita buscar no banco a cada requisição para obter dados do usuário logado
            $_SESSION["user_data"] = $row; // Armazena o array completo retornado por findByEmail
            $_SESSION["user_id"] = $row["id"]; // Manter user_id para compatibilidade e verificações rápidas
            
            // Opcional: remover chaves individuais antigas se existirem
            unset($_SESSION["user_name"]);
            unset($_SESSION["user_email"]);

            return true;
        }

        return false;
    }


    /**
     * Método para verificar se usuário está logado
     * 
     * @return boolean Status do login
     */
    public function isLoggedIn()
    {
        // Verifica se o ID do usuário está na sessão
        return isset($_SESSION["user_id"]);
    }

    /**
     * Método para obter dados do usuário logado
     * 
     * @return array|null Dados do usuário ou null se não estiver logado
     */
    public function getLoggedInUser()
    {
        // Retorna os dados completos do usuário armazenados na sessão
        if ($this->isLoggedIn() && isset($_SESSION["user_data"])) {
            return $_SESSION["user_data"]; // Retorna o array completo
        }

        // Se não estiver logado ou os dados não estiverem na sessão, retorna null
        return null;
    }

    /**
     * Método para fazer logout
     * 
     * @return boolean Resultado da operação
     */
    public function logout()
    {
        // Destruir todas as variáveis de sessão
        $_SESSION = array();

        // Destruir o cookie da sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                "",
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destruir a sessão
        session_destroy();

        return true;
    }
}

