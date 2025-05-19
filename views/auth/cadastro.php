<?php
// Página de cadastro de funcionários utilizando POO com estrutura reorganizada
// Usando caminho absoluto para o init.php

// Definir caminho absoluto para a raiz do projeto
$rootPath = dirname(dirname(__DIR__));
require_once $rootPath . '/init.php';

// Inicializar objetos
$database = new Database(Config::$DB_HOST, Config::$DB_NAME, Config::$DB_USER, Config::$DB_PASS);
$db = $database->connect();
$user = new User($db);
$auth = new Auth($user);

// Verificar se o usuário está logado
if (!$auth->isLoggedIn()) {
    // Redirecionar para a página de login
    header("Location: " . Config::$LOGIN_URL);
    exit;
}

// Obter informações do usuário logado
$loggedUser = $auth->getLoggedInUser();
$user_id = $loggedUser['id'];
$user_name = $loggedUser['name'];
$user_email = $loggedUser['email'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionários - Sistema de Gerenciamento de Estoque</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Config::$BASE_URL; ?>/assets/css/cadastro-styles.css">
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
                    <li class="active"><a href="#"><i class="fas fa-users"></i> Funcionários</a></li>
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
                    
                    <form id="employee-form" class="form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullname">Nome Completo *</label>
                                <div class="input-group">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="fullname" name="fullname" placeholder="Nome completo do funcionário" required>
                                </div>
                                <span class="error-message" id="fullname-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <div class="input-group">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" placeholder="Email para login" required>
                                </div>
                                <span class="error-message" id="email-error"></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Senha *</label>
                                <div class="input-group">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="password" name="password" placeholder="Senha" required>
                                    <button type="button" class="toggle-password" aria-label="Mostrar senha">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <span class="error-message" id="password-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm-password">Confirmar Senha *</label>
                                <div class="input-group">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirme a senha" required>
                                </div>
                                <span class="error-message" id="confirm-password-error"></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="job-title">Cargo/Função *</label>
                                <div class="input-group">
                                    <i class="fas fa-briefcase"></i>
                                    <input type="text" id="job-title" name="job-title" placeholder="Cargo ou função" required>
                                </div>
                                <span class="error-message" id="job-title-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="department">Departamento *</label>
                                <div class="input-group">
                                    <i class="fas fa-building"></i>
                                    <input type="text" id="department" name="department" placeholder="Departamento" required>
                                </div>
                                <span class="error-message" id="department-error"></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="access-level">Nível de Acesso *</label>
                                <div class="input-group select-group">
                                    <i class="fas fa-shield-alt"></i>
                                    <select id="access-level" name="access-level" required>
                                        <option value="" disabled selected>Selecione o nível de acesso</option>
                                        <option value="admin">Administrador</option>
                                        <option value="manager">Gerente</option>
                                        <option value="operator">Operador</option>
                                    </select>
                                </div>
                                <span class="error-message" id="access-level-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Telefone</label>
                                <div class="input-group">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" id="phone" name="phone" placeholder="(00) 00000-0000">
                                </div>
                                <span class="error-message" id="phone-error"></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="admission-date">Data de Admissão *</label>
                                <div class="input-group">
                                    <i class="fas fa-calendar"></i>
                                    <input type="date" id="admission-date" name="admission-date" required>
                                </div>
                                <span class="error-message" id="admission-date-error"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status *</label>
                                <div class="input-group select-group">
                                    <i class="fas fa-toggle-on"></i>
                                    <select id="status" name="status" required>
                                        <option value="active" selected>Ativo</option>
                                        <option value="inactive">Inativo</option>
                                    </select>
                                </div>
                                <span class="error-message" id="status-error"></span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="notes">Observações</label>
                                <div class="input-group">
                                    <i class="fas fa-sticky-note"></i>
                                    <textarea id="notes" name="notes" placeholder="Observações adicionais sobre o funcionário"></textarea>
                                </div>
                                <span class="error-message" id="notes-error"></span>
                            </div>
                        </div>
                        
                        <div class="form-row checkbox-row">
                            <div class="form-group">
                                <label class="checkbox-container">
                                    <input type="checkbox" id="generate-password" name="generate-password">
                                    <span class="checkmark"></span>
                                    Gerar senha automática e enviar por email
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="secondary-button" id="clear-button">
                                <i class="fas fa-eraser"></i>
                                Limpar
                            </button>
                            <button type="submit" class="primary-button" id="save-button">
                                <span class="button-text">
                                    <i class="fas fa-save"></i>
                                    Salvar
                                </span>
                                <span class="spinner"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal" id="success-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Cadastro Realizado</h3>
                <button class="close-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <p>Funcionário cadastrado com sucesso!</p>
                <p class="modal-details">Um email com as instruções de acesso foi enviado para o endereço cadastrado.</p>
            </div>
            <div class="modal-footer">
                <button class="secondary-button close-modal">Fechar</button>
                <button class="primary-button" id="new-employee-button">Cadastrar Outro</button>
            </div>
        </div>
    </div>
    
    <script src="<?php echo Config::$BASE_URL; ?>/assets/js/cadastro-script.js"></script>
</body>
</html>
