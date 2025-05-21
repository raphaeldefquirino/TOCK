# Sistema TOCK - Correções e Instruções

Este documento contém instruções para a correção do sistema TOCK, especificamente relacionadas ao fluxo de autenticação e estrutura de arquivos.

## Problemas Identificados

1. **Ausência da tela de login**: Tanto o arquivo `login.php` quanto `cadastro.php` continham o mesmo código da tela de cadastro de funcionários.
2. **Inconsistências nos caminhos**: Referências incorretas a arquivos CSS, JS e PHP.
3. **Nomes de arquivos de classes em minúsculo**: Incompatibilidade com o autoload em alguns sistemas.
4. **Problemas no fluxo de redirecionamento**: Causando loops infinitos de redirecionamento.

## Correções Implementadas

1. **Restauração da tela de login**: Criação de uma tela de login funcional e integrada ao backend.
2. **Correção dos caminhos**: Uso de caminhos absolutos para garantir funcionamento em qualquer ambiente.
3. **Ajuste do autoload**: Modificação para suportar nomes de arquivos em minúsculo.
4. **Correção do fluxo de autenticação**: Garantindo que apenas usuários autenticados acessem a página de cadastro.
5. **Padronização da estrutura de arquivos**: Seguindo as melhores práticas de POO e MVC.

## Estrutura de Arquivos

```
/TOCK/
├── assets/
│   ├── css/
│   │   ├── style-login.css
│   │   └── style-cadastro.css
│   └── js/
│       ├── view-login.js
│       └── view-cadastro.js
├── classes/
│   ├── Auth.php
│   ├── User.php
│   └── Validator.php
├── config/
│   ├── Config.php
│   └── Database.php
├── views/
│   └── auth/
│       ├── login.php
│       ├── cadastro.php
│       └── logout.php
├── index.php
└── init.php
```

## Instruções de Instalação

1. **Backup**: Faça um backup completo da sua pasta TOCK atual.
2. **Substituição de arquivos**: Substitua os arquivos existentes pelos novos arquivos corrigidos.
3. **Verificação de permissões**: Certifique-se de que as permissões dos arquivos estão corretas.
4. **Configuração do banco de dados**: Verifique se as configurações em `config/Config.php` estão corretas para seu ambiente.

## Criação de Usuário Inicial

Para testar o sistema, você precisará criar pelo menos um usuário no banco de dados. Execute o seguinte SQL:

```sql
INSERT INTO usuarios (nome, email, senha, data_criacao) 
VALUES ('Administrador', 'admin@exemplo.com', '$2y$10$8tDjZJJUkMbDjLOKQx3Kn.1BVMZBTLUEKZIQImtYOEQCBn7C4dKJe', NOW());
```

A senha para este usuário é: `admin123`

## Fluxo de Funcionamento

1. Acesse a raiz do projeto (`/TOCK/`) e você será redirecionado para a tela de login.
2. Faça login com as credenciais do usuário criado.
3. Após autenticação bem-sucedida, você será redirecionado para a tela de cadastro de funcionários.
4. Para sair, clique no botão "Sair" na barra lateral.

## Observações Importantes

- O sistema agora utiliza programação orientada a objetos (POO) de forma consistente.
- A estrutura segue o padrão MVC, com clara separação entre views, classes e configurações.
- O autoload foi ajustado para funcionar com os nomes de arquivos em minúsculo existentes.
- Todas as senhas são armazenadas com hash seguro usando `password_hash()`.

Em caso de dúvidas ou problemas, entre em contato com o suporte técnico.
