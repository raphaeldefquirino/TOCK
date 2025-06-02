<?php
// Página de login utilizando POO com estrutura reorganizada
// Usando caminho absoluto para o init.php

// Definir caminho absoluto para a raiz do projeto
$rootPath = dirname(dirname(__DIR__));
require_once $rootPath . '/init.php';

// Inicializar objetos
$database = new Database(Config::$DB_HOST, Config::$DB_NAME, Config::$DB_USER, Config::$DB_PASS);
$db = $database->connect();
$user = new User($db);
$auth = new Auth($user);
$validator = new Validator();

// Variáveis para mensagens
$error_message = '';
$success_message = '';

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Validar campos
    $valid_email = $validator->validateEmail($email);
    $valid_password = $validator->validatePassword($password);
    
    if ($valid_email && $valid_password) {
        // Tentar fazer login
        if ($auth->login($email, $password)) {
            // Login bem-sucedido, redirecionar para a página de cadastro
            header("Location: " . Config::$CADASTRO_URL);
            exit;
        } else {
            $error_message = 'Email ou senha incorretos. Por favor, tente novamente.';
        }
    } else {
        // Exibir erros de validação
        $errors = $validator->getErrors();
        if (isset($errors['email'])) {
            $error_message = $errors['email'];
        } elseif (isset($errors['password'])) {
            $error_message = $errors['password'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gerenciamento de Estoque</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Config::$BASE_URL; ?>/assets/css/style-login.css">
</head>
<body>
   <div class="container">
        <div class="login-container">
            <div class="login-header">
                <div class="logo">
                    <i class="fas fa-boxes-stacked"></i>
                    <h1>Tock</h1>
                </div>
                <p class="subtitle">Sistema de Gerenciamento de Estoque</p>
            </div>
            
            <div class="login-form-container">
                <div class="form-header">
                    <h2>Bem-vindo de volta</h2>
                    <p>Faça login para acessar o sistema</p>
                </div>
                
                <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <form id="login-form" class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="Seu email" required>
                        </div>
                        <span class="error-message" id="email-error"></span>
                    </div>
                    
                    <div class="form-group">
                        <div class="password-header">
                            <label for="password">Senha</label>
                            <a href="#" class="forgot-password">Esqueceu a senha?</a>
                        </div>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Sua senha" required>
                            <button type="button" class="toggle-password" aria-label="Mostrar senha">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="error-message" id="password-error"></span>
                    </div>
                    
                    <div class="form-group remember-me">
                        <label class="checkbox-container">
                            <input type="checkbox" id="remember" name="remember">
                            <span class="checkmark"></span>
                            Lembrar-me
                        </label>
                    </div>
                    
                    <button type="submit" class="login-button">
                        <span class="button-text">Entrar</span>
                        <span class="spinner"></span>
                    </button>
                </form>
                
                <div class="login-footer">
                    <p>Não tem uma conta? <a href="#" class="register-link">Fale com o administrador</a></p>
                </div>
            </div>
        </div>
        
        <div class="login-image">
            <div class="image-overlay"></div>
            <div class="quote-container">
                <blockquote>
                    "Otimize seu estoque, maximize seus resultados."
                </blockquote>
                <p class="quote-author">StockPro</p>
            </div>
        </div>
    </div>
    
    <script src="<?php echo Config::$BASE_URL; ?>/assets/js/view-login.js"></script>
</body>
</html>
