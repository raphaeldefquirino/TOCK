<?php
// Página de consulta de funcionários utilizando POO com estrutura reorganizada
// Usando caminho absoluto para o init.php

// Definir caminho absoluto para a raiz do projeto
$rootPath = dirname(dirname(__DIR__));
require_once $rootPath . "/init.php";

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
if (!$loggedUser) {
    // Se não conseguir obter dados do usuário logado (sessão inválida?), redirecionar para login
    $auth->logout();
    header("Location: " . Config::$LOGIN_URL . "?error=session_invalid");
    exit;
}
$user_id = $loggedUser["id"];
$user_name = isset($loggedUser["nome"]) ? $loggedUser["nome"] : "Usuário"; 
$user_email = isset($loggedUser["email"]) ? $loggedUser["email"] : "email@desconhecido.com";

// Buscar todos os funcionários
$funcionarios = [];
try {
    $query = "SELECT id, nome, email, cargo, departamento, nivel_acesso, telefone, data_admissao, status, observacoes, data_criacao 
              FROM usuarios 
              ORDER BY nome ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errorMessage = "Erro ao buscar funcionários: " . $e->getMessage();
}

// Função para formatar a data
function formatarData($data) {
    if (empty($data)) return "-";
    $timestamp = strtotime($data);
    return date('d/m/Y', $timestamp);
}

// Função para traduzir o nível de acesso
function traduzirNivelAcesso($nivel) {
    $niveis = [
        'admin' => 'Administrador',
        'manager' => 'Gerente',
        'operator' => 'Operador'
    ];
    return $niveis[$nivel] ?? $nivel;
}

// Função para traduzir o status
function traduzirStatus($status) {
    $statusTraduzido = [
        'active' => 'Ativo',
        'inactive' => 'Inativo'
    ];
    return $statusTraduzido[$status] ?? $status;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Funcionários - Sistema de Gerenciamento de Estoque</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo Config::$BASE_URL; ?>/assets/css/style-cadastro.css">
    <style>
        /* Estilos adicionais para a tabela de consulta */
        .table-container {
            width: 100%;
            overflow-x: auto;
            margin-top: 20px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .data-table th, 
        .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }
        
        .data-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .data-table .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .data-table .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .data-table .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            padding: 4px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .btn-view {
            color: #0d6efd;
        }
        
        .btn-edit {
            color: #ffc107;
        }
        
        .btn-delete {
            color: #dc3545;
        }
        
        .btn-action:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .btn-action.disabled {
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.5;
        }
        
        .btn-action.disabled:hover {
            background-color: transparent;
        }
        
        .search-filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .search-box {
            display: flex;
            align-items: center;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 8px 12px;
            flex: 1;
            max-width: 400px;
        }
        
        .search-box input {
            border: none;
            outline: none;
            width: 100%;
            padding: 0 8px;
            font-size: 0.95rem;
        }
        
        .search-box i {
            color: #6c757d;
        }
        
        .filter-options {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .filter-select {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            background-color: #fff;
            font-size: 0.95rem;
            outline: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .empty-state p {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        
        .btn-add-new {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            font-weight: 500;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        
        .btn-add-new:hover {
            background-color: #218838;
        }
        
        /* Responsividade para telas menores */
        @media (max-width: 768px) {
            .search-filter-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                max-width: 100%;
            }
            
            .filter-options {
                flex-wrap: wrap;
            }
            
            .data-table {
                font-size: 0.9rem;
            }
            
            .data-table th, 
            .data-table td {
                padding: 10px;
            }
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
                    <li class="active">
                        <a href="#"><i class="fas fa-users"></i> Funcionários</a>
                        <ul class="submenu">
                            <li><a href="<?php echo Config::$BASE_URL; ?>/views/auth/cadastro.php">Cadastrar</a></li>
                            <li><a href="<?php echo Config::$BASE_URL; ?>/views/auth/consulta_funcionarios.php" class="active">Consultar</a></li>
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
                <div class="header-title">
                    <h2>Consulta de Funcionários</h2>
                    <p>Visualize e gerencie os funcionários cadastrados</p>
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
                        <h3>Funcionários Cadastrados</h3>
                        <p>Lista de todos os funcionários do sistema</p>
                    </div>
                    
                    <?php if (isset($errorMessage)): ?>
                        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
                    
                    <div class="search-filter-container">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search-input" placeholder="Buscar funcionário..." onkeyup="filterTable()">
                        </div>
                        
                        <div class="filter-options">
                            <select id="status-filter" class="filter-select" onchange="filterTable()">
                                <option value="">Todos os status</option>
                                <option value="Ativo">Ativos</option>
                                <option value="Inativo">Inativos</option>
                            </select>
                            
                            <select id="department-filter" class="filter-select" onchange="filterTable()">
                                <option value="">Todos os departamentos</option>
                                <?php 
                                $departamentos = [];
                                foreach ($funcionarios as $funcionario) {
                                    if (!in_array($funcionario['departamento'], $departamentos)) {
                                        $departamentos[] = $funcionario['departamento'];
                                        echo '<option value="' . htmlspecialchars($funcionario['departamento']) . '">' . htmlspecialchars($funcionario['departamento']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            
                            <a href="<?php echo Config::$BASE_URL; ?>/views/auth/cadastro.php" class="btn-add-new">
                                <i class="fas fa-plus"></i>
                                <span>Novo Funcionário</span>
                            </a>
                        </div>
                    </div>
                    
                    <div class="table-container">
                        <?php if (empty($funcionarios)): ?>
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>Nenhum funcionário cadastrado no sistema.</p>
                                <a href="<?php echo Config::$BASE_URL; ?>/views/auth/cadastro.php" class="btn-add-new">
                                    <i class="fas fa-plus"></i>
                                    <span>Cadastrar Funcionário</span>
                                </a>
                            </div>
                        <?php else: ?>
                            <table class="data-table" id="funcionarios-table">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Cargo</th>
                                        <th>Departamento</th>
                                        <th>Nível de Acesso</th>
                                        <th>Data de Admissão</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($funcionarios as $funcionario): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($funcionario['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($funcionario['email']); ?></td>
                                            <td><?php echo htmlspecialchars($funcionario['cargo']); ?></td>
                                            <td><?php echo htmlspecialchars($funcionario['departamento']); ?></td>
                                            <td><?php echo htmlspecialchars(traduzirNivelAcesso($funcionario['nivel_acesso'])); ?></td>
                                            <td><?php echo formatarData($funcionario['data_admissao']); ?></td>
                                            <td>
                                                <span class="status <?php echo $funcionario['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                                    <?php echo htmlspecialchars(traduzirStatus($funcionario['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button type="button" class="btn-action btn-view" title="Visualizar detalhes" onclick="alert('Visualizar detalhes do funcionário ID: <?php echo $funcionario['id']; ?>')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn-action btn-edit disabled" title="Editar (em breve)" disabled>
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn-action btn-delete disabled" title="Excluir (em breve)" disabled>
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Função para filtrar a tabela
        function filterTable() {
            const searchInput = document.getElementById('search-input').value.toLowerCase();
            const statusFilter = document.getElementById('status-filter').value;
            const departmentFilter = document.getElementById('department-filter').value;
            const table = document.getElementById('funcionarios-table');
            
            if (!table) return; // Se não houver tabela, não faz nada
            
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const nome = rows[i].cells[0].textContent.toLowerCase();
                const email = rows[i].cells[1].textContent.toLowerCase();
                const cargo = rows[i].cells[2].textContent.toLowerCase();
                const departamento = rows[i].cells[3].textContent;
                const status = rows[i].cells[6].textContent.trim();
                
                const matchesSearch = nome.includes(searchInput) || 
                                     email.includes(searchInput) || 
                                     cargo.includes(searchInput);
                                     
                const matchesStatus = statusFilter === '' || status === statusFilter;
                const matchesDepartment = departmentFilter === '' || departamento === departmentFilter;
                
                if (matchesSearch && matchesStatus && matchesDepartment) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
        
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
