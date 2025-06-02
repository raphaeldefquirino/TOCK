<?php
// Página de cadastro de funcionários utilizando POO com estrutura reorganizada
// Usando caminho absoluto para o init.php

// Definir caminho absoluto para a raiz do projeto
$rootPath = dirname(dirname(__DIR__));
require_once $rootPath . "/init.php";

// --- DEBUG: Verificar se o POST está chegando --- 
// if ($_SERVER["REQUEST_METHOD"] === "POST") { 
//     echo "<pre>DEBUG: Dados POST recebidos:\n";
//     print_r($_POST);
//     echo "</pre>";
//     // die("DEBUG: POST recebido! Processamento PHP iniciado."); // Descomente para parar aqui
// }
// --- FIM DEBUG ---

// Inicializar objetos
$database = new Database(Config::$DB_HOST, Config::$DB_NAME, Config::$DB_USER, Config::$DB_PASS);
$db = $database->connect();
$user = new User($db);
$auth = new Auth($user);
$validator = new Validator(); // Instanciar o validador

// Verificar se o usuário está logado
if (!$auth->isLoggedIn()) {
    // Redirecionar para a página de login
    header("Location: " . Config::$LOGIN_URL);
    exit;
}

// Obter informações do usuário logado
$loggedUser = $auth->getLoggedInUser();
if (!$loggedUser) {
    // Se não conseguir obter dados do usuário logado (sessão inválida?), redirecionar para login
    $auth->logout();
    header("Location: " . Config::$LOGIN_URL . "?error=session_invalid");
    exit;
}
$user_id = $loggedUser["id"];
// Certifique-se de que a chave 'nome' existe antes de usá-la
$user_name = isset($loggedUser["nome"]) ? $loggedUser["nome"] : "Usuário"; 
$user_email = isset($loggedUser["email"]) ? $loggedUser["email"] : "email@desconhecido.com";

// Variáveis para mensagens de feedback
$successMessage = "";
$errorMessage = "";
$errors = []; // Array para armazenar erros de validação manual

// Processar o formulário quando enviado (método POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obter dados do formulário
    $fullname = $_POST["fullname"] ?? "";
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm-password"] ?? "";
    $jobTitle = $_POST["job-title"] ?? "";
    $department = $_POST["department"] ?? "";
    $accessLevel = $_POST["access-level"] ?? "";
    $phone = $_POST["phone"] ?? "";
    $admissionDate = $_POST["admission-date"] ?? "";
    $status = $_POST["status"] ?? "active"; // Default to active
    $notes = $_POST["notes"] ?? "";
    $generatePassword = isset($_POST["generate-password"]); // Checkbox

    // --- Validação --- 
    $validator->validateName($fullname);
    $validator->validateEmail($email);
    
    if ($user->emailExists($email)) {
        $validator->errors["email"] = "Este email já está cadastrado."; 
    }

    if (!$generatePassword) { 
        $validator->validatePassword($password); 
        if ($password !== $confirmPassword) {
             $errors["confirm-password"] = "As senhas não coincidem.";
        }
    } else {
        $password = bin2hex(random_bytes(8)); 
    }

    if (empty($jobTitle)) $errors["job-title"] = "Cargo/Função é obrigatório.";
    if (empty($department)) $errors["department"] = "Departamento é obrigatório.";
    if (empty($accessLevel)) $errors["access-level"] = "Nível de acesso é obrigatório.";
    if (empty($admissionDate)) $errors["admission-date"] = "Data de admissão é obrigatória.";
    if (empty($status)) $errors["status"] = "Status é obrigatório.";

    $allErrors = array_merge($validator->getErrors(), $errors);

    if (empty($allErrors)) {
        $user->nome = $fullname;
        $user->email = $email;
        $user->senha = $password; 
        $user->cargo = $jobTitle;
        $user->departamento = $department;
        $user->nivel_acesso = $accessLevel;
        $user->telefone = $phone;
        $user->data_admissao = $admissionDate;
        $user->status = $status;
        $user->observacoes = $notes;

        $createResult = $user->create();
        if ($createResult === true) {
            $successMessage = "Funcionário cadastrado com sucesso!";
            if ($generatePassword) {
                $successMessage .= " Senha gerada (teste): " . $password;
            }
            $_POST = []; // Limpar POST para limpar formulário
        } else {
            // Se create() retornou uma string de erro
            $errorMessage = "Erro ao cadastrar funcionário: " . htmlspecialchars($createResult);
        }
    } else {
        $errorMessage = "Por favor, corrija os erros no formulário.";
        $errors = $allErrors; 
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionários - Sistema de Gerenciamento de Estoque</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Config::$BASE_URL; ?>/assets/css/style-cadastro.css">
    <style>
        /* Estilos (inalterados) */
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .error-message { color: #dc3545; font-size: 0.875em; margin-top: 0.25rem; }
        .input-group input.is-invalid, .input-group select.is-invalid, .input-group textarea.is-invalid { border-color: #dc3545; }
        .spinner { border: 4px solid rgba(0, 0, 0, 0.1); width: 16px; height: 16px; border-radius: 50%; border-left-color: #09f; animation: spin 1s ease infinite; display: inline-block; vertical-align: middle; margin-left: 5px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        /* Estilos para submenu */
        .sidebar-nav .submenu {
            display: none;
            padding-left: 20px;
            margin-top: 5px;
        }
        
        .sidebar-nav .submenu li {
            margin-bottom: 5px;
        }
        
        .sidebar-nav .submenu a {
            font-size: 0.9em;
            padding: 8px 10px;
            border-radius: 4px;
            display: block;
            color: #f8f9fa;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        
        .sidebar-nav .submenu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-nav .submenu a.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 500;
        }
        
        .sidebar-nav li.has-submenu > a {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .sidebar-nav li.has-submenu > a::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            transition: transform 0.2s;
        }
        
        .sidebar-nav li.has-submenu.expanded > a::after {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-boxes-stacked"></i>
                    <h1>StockPro</h1>
                </div>
                <p class="subtitle">Sistema de Gerenciamento de Estoque</p>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="active has-submenu">
                        <a href="#"><i class="fas fa-users"></i> Funcionários</a>
                        <ul class="submenu" style="display: block;">
                            <li><a href="<?php echo Config::$BASE_URL; ?>/views/auth/cadastro.php" class="active">Cadastrar</a></li>
                            <li><a href="<?php echo Config::$BASE_URL; ?>/views/auth/consulta_funcionarios.php">Consultar</a></li>
                        </ul>
                    </li>
                    <li><a href="#"><i class="fas fa-box"></i> Produtos</a></li>
                    <li><a href="#"><i class="fas fa-chart-line"></i> Relatórios</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Configurações</a></li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <a href="<?php echo Config::$BASE_URL; ?>/views/auth/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </a>
            </div>
        </div>
        
        <div class="main-content">
            <header class="content-header">
                 <!-- Header content (inalterado) -->
                 <div class="header-title">
                    <h2>Cadastro de Funcionários</h2>
                    <p>Adicione novos usuários ao sistema</p>
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </header>
            
            <div class="content-body">
                <div class="card">
                    <div class="card-header">
                        <h3>Informações do Funcionário</h3>
                        <p>Preencha todos os campos obrigatórios (*)</p>
                    </div>

                    <!-- Mensagens de Feedback -->
                    <?php if (!empty($successMessage)): ?>
                        <div class="alert alert-success"><?php echo $successMessage; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
                    
                    <!-- Formulário HTML -->
                    <form id="employee-form" class="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
                        <!-- Campos do formulário (inalterados, mas com classes de erro PHP) -->
                         <div class="form-row">
                            <div class="form-group">
                                <label for="fullname">Nome Completo *</label>
                                <div class="input-group">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="fullname" name="fullname" placeholder="Nome completo do funcionário" required value="<?php echo isset($_POST["fullname"]) ? htmlspecialchars($_POST["fullname"]) : ""; ?>" class="<?php echo isset($errors["name"]) ? "is-invalid" : ""; ?>">
                                </div>
                                <span class="error-message" id="fullname-error"><?php echo $errors["name"] ?? ""; ?></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <div class="input-group">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" placeholder="Email para login" required value="<?php echo isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : ""; ?>" class="<?php echo isset($errors["email"]) ? "is-invalid" : ""; ?>">
                                </div>
                                <span class="error-message" id="email-error"><?php echo $errors["email"] ?? ""; ?></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Senha *</label>
                                <div class="input-group">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="password" name="password" placeholder="Senha (mín. 6 caracteres)" required class="<?php echo isset($errors["password"]) ? "is-invalid" : ""; ?>">
                                    <button type="button" class="toggle-password" aria-label="Mostrar senha">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <span class="error-message" id="password-error"><?php echo $errors["password"] ?? ""; ?></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm-password">Confirmar Senha *</label>
                                <div class="input-group">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirme a senha" required class="<?php echo isset($errors["confirm-password"]) ? "is-invalid" : ""; ?>">
                                </div>
                                <span class="error-message" id="confirm-password-error"><?php echo $errors["confirm-password"] ?? ""; ?></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="job-title">Cargo/Função *</label>
                                <div class="input-group">
                                    <i class="fas fa-briefcase"></i>
                                    <input type="text" id="job-title" name="job-title" placeholder="Cargo ou função" required value="<?php echo isset($_POST["job-title"]) ? htmlspecialchars($_POST["job-title"]) : ""; ?>" class="<?php echo isset($errors["job-title"]) ? "is-invalid" : ""; ?>">
                                </div>
                                <span class="error-message" id="job-title-error"><?php echo $errors["job-title"] ?? ""; ?></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="department">Departamento *</label>
                                <div class="input-group">
                                    <i class="fas fa-building"></i>
                                    <input type="text" id="department" name="department" placeholder="Departamento" required value="<?php echo isset($_POST["department"]) ? htmlspecialchars($_POST["department"]) : ""; ?>" class="<?php echo isset($errors["department"]) ? "is-invalid" : ""; ?>">
                                </div>
                                <span class="error-message" id="department-error"><?php echo $errors["department"] ?? ""; ?></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="access-level">Nível de Acesso *</label>
                                <div class="input-group select-group">
                                    <i class="fas fa-shield-alt"></i>
                                    <select id="access-level" name="access-level" required class="<?php echo isset($errors["access-level"]) ? "is-invalid" : ""; ?>">
                                        <option value="" disabled <?php echo empty($_POST["access-level"]) ? "selected" : ""; ?>>Selecione o nível de acesso</option>
                                        <option value="admin" <?php echo (isset($_POST["access-level"]) && $_POST["access-level"] == "admin") ? "selected" : ""; ?>>Administrador</option>
                                        <option value="manager" <?php echo (isset($_POST["access-level"]) && $_POST["access-level"] == "manager") ? "selected" : ""; ?>>Gerente</option>
                                        <option value="operator" <?php echo (isset($_POST["access-level"]) && $_POST["access-level"] == "operator") ? "selected" : ""; ?>>Operador</option>
                                    </select>
                                </div>
                                <span class="error-message" id="access-level-error"><?php echo $errors["access-level"] ?? ""; ?></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Telefone</label>
                                <div class="input-group">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" id="phone" name="phone" placeholder="(00) 00000-0000" value="<?php echo isset($_POST["phone"]) ? htmlspecialchars($_POST["phone"]) : ""; ?>">
                                </div>
                                <span class="error-message" id="phone-error"><?php echo $errors["phone"] ?? ""; ?></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="admission-date">Data de Admissão *</label>
                                <div class="input-group">
                                    <i class="fas fa-calendar"></i>
                                    <input type="date" id="admission-date" name="admission-date" required value="<?php echo isset($_POST["admission-date"]) ? htmlspecialchars($_POST["admission-date"]) : ""; ?>" class="<?php echo isset($errors["admission-date"]) ? "is-invalid" : ""; ?>">
                                </div>
                                <span class="error-message" id="admission-date-error"><?php echo $errors["admission-date"] ?? ""; ?></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status *</label>
                                <div class="input-group select-group">
                                    <i class="fas fa-toggle-on"></i>
                                    <select id="status" name="status" required class="<?php echo isset($errors["status"]) ? "is-invalid" : ""; ?>">
                                        <option value="active" <?php echo (!isset($_POST["status"]) || (isset($_POST["status"]) && $_POST["status"] == "active")) ? "selected" : ""; ?>>Ativo</option>
                                        <option value="inactive" <?php echo (isset($_POST["status"]) && $_POST["status"] == "inactive") ? "selected" : ""; ?>>Inativo</option>
                                    </select>
                                </div>
                                <span class="error-message" id="status-error"><?php echo $errors["status"] ?? ""; ?></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="notes">Observações</label>
                                <div class="input-group">
                                    <i class="fas fa-sticky-note"></i>
                                    <textarea id="notes" name="notes" placeholder="Observações adicionais sobre o funcionário"><?php echo isset($_POST["notes"]) ? htmlspecialchars($_POST["notes"]) : ""; ?></textarea>
                                </div>
                                <span class="error-message" id="notes-error"><?php echo $errors["notes"] ?? ""; ?></span>
                            </div>
                        </div>
                        
                        <div class="form-row checkbox-row">
                            <div class="form-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" id="generate-password" name="generate-password" <?php echo isset($_POST["generate-password"]) ? "checked" : ""; ?>>
                                    <span class="checkmark"></span>
                                    Gerar senha automática (envio por email não implementado)
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="secondary-button" id="clear-button" onclick="document.getElementById('employee-form').reset(); document.querySelectorAll('.error-message').forEach(el => el.textContent = ''); document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid')); return false;">
                                <i class="fas fa-eraser"></i>
                                Limpar
                            </button>
                            <button type="submit" class="primary-button" id="save-button">
                                <span class="button-text">
                                    <i class="fas fa-save"></i>
                                    Salvar
                                </span>
                                <span class="spinner" style="display: none;"></span> <!-- Spinner oculto inicialmente -->
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal (não utilizado ativamente no fluxo PHP atual) -->
    <div class="modal" id="success-modal" style="display: none;"> 
        <!-- Conteúdo do modal -->
    </div>
    
    <!-- Removido JS de view-cadastro.js para simplificar -->
    <!-- <script src="<?php echo Config::$BASE_URL; ?>/assets/js/view-cadastro.js"></script> --> 
    <script>
        // Script para mostrar/ocultar senha
        document.querySelectorAll(".toggle-password").forEach(button => {
            button.addEventListener("click", function() {
                const passwordInput = this.previousElementSibling;
                const icon = this.querySelector("i");
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    icon.classList.remove("fa-eye");
                    icon.classList.add("fa-eye-slash");
                } else {
                    passwordInput.type = "password";
                    icon.classList.remove("fa-eye-slash");
                    icon.classList.add("fa-eye");
                }
            });
        });

        // Desabilitar/Habilitar campos de senha com base no checkbox
        const generatePasswordCheckbox = document.getElementById("generate-password");
        const passwordInput = document.getElementById("password");
        const confirmPasswordInput = document.getElementById("confirm-password");

        function togglePasswordFields() {
            const disabled = generatePasswordCheckbox.checked;
            passwordInput.disabled = disabled;
            confirmPasswordInput.disabled = disabled;
            passwordInput.required = !disabled;
            confirmPasswordInput.required = !disabled;
            if (disabled) {
                passwordInput.classList.remove("is-invalid");
                confirmPasswordInput.classList.remove("is-invalid");
                document.getElementById("password-error").textContent = "";
                document.getElementById("confirm-password-error").textContent = "";
            }
        }

        generatePasswordCheckbox.addEventListener("change", togglePasswordFields);
        togglePasswordFields(); 

        // Adicionar submenu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuItems = document.querySelectorAll('.sidebar-nav > ul > li');
            
            menuItems.forEach(item => {
                const submenu = item.querySelector('.submenu');
                if (submenu) {
                    // Se já tem submenu, adicionar classe para estilização
                    item.classList.add('has-submenu');
                    
                    // Se contém link ativo, expandir submenu
                    if (submenu.querySelector('.active')) {
                        submenu.style.display = 'block';
                        item.classList.add('expanded');
                    }
                    
                    // Toggle no clique
                    item.querySelector('a').addEventListener('click', function(e) {
                        if (e.target.tagName === 'A' && e.target.nextElementSibling && e.target.nextElementSibling.classList.contains('submenu')) {
                            e.preventDefault();
                            item.classList.toggle('expanded');
                            submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
