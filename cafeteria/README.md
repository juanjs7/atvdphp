# ☕ Sistema de Gestão de Cardápio - Cafeteria

CRUD completo em **PHP procedural + MySQLi** com login e Prepared Statements.

## Funcionalidades
- Login com sessão (`login.php`) e bloqueio de páginas para quem não está logado
- **C**reate: cadastro de itens no `painel.php`
- **R**ead: listagem em tabela no `painel.php`
- **U**pdate: edição em `editar_item.php?id=X`
- **D**elete: exclusão em `excluir_item.php?id=X`
- Todas as consultas usam `mysqli_prepare` (proteção contra SQL Injection)

## Como rodar
1. Instale o XAMPP e inicie **Apache** e **MySQL**.
2. No phpMyAdmin, execute o arquivo `banco.sql`.
3. Copie esta pasta para `C:\xampp\htdocs\cafeteria`.
4. Acesse `http://localhost/cafeteria/login.php`
5. Login: **admin** / Senha: **admin123**

## Arquivos
| Arquivo | Função |
|---|---|
| conexao.php | Conexão com o banco |
| verifica_login.php | Protege as páginas (redireciona para o login) |
| login.php / logout.php | Entrar e sair |
| painel.php | Cadastro + listagem |
| editar_item.php | Edição de item |
| excluir_item.php | Exclusão de item |
| style.css | Estilo visual |
| banco.sql | Script do banco de dados |
