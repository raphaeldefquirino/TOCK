<?php
/**
 * Classe Validator
 * Responsável pela validação de dados de formulários
 */
class Validator {
    private $errors = [];
    
    /**
     * Método para validar email
     * 
     * @param string $email Email a ser validado
     * @return boolean Resultado da validação
     */
    public function validateEmail($email) {
        if(empty($email)) {
            $this->errors['email'] = "Email é obrigatório";
            return false;
        }
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Email inválido";
            return false;
        }
        
        return true;
    }
    
    /**
     * Método para validar senha
     * 
     * @param string $password Senha a ser validada
     * @return boolean Resultado da validação
     */
    public function validatePassword($password) {
        if(empty($password)) {
            $this->errors['password'] = "Senha é obrigatória";
            return false;
        }
        
        if(strlen($password) < 6) {
            $this->errors['password'] = "Senha deve ter pelo menos 6 caracteres";
            return false;
        }
        
        return true;
    }
    
    /**
     * Método para validar nome
     * 
     * @param string $name Nome a ser validado
     * @return boolean Resultado da validação
     */
    public function validateName($name) {
        if(empty($name)) {
            $this->errors['name'] = "Nome é obrigatório";
            return false;
        }
        
        if(strlen($name) < 3) {
            $this->errors['name'] = "Nome deve ter pelo menos 3 caracteres";
            return false;
        }
        
        return true;
    }
    
    /**
     * Método para obter erros
     * 
     * @return array Lista de erros
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Método para verificar se há erros
     * 
     * @return boolean Se há erros ou não
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
}
