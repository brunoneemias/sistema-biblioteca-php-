# 📚 Sistema de Gerenciamento de Biblioteca – PHP

Este projeto é um sistema web desenvolvido em PHP para gerenciar uma biblioteca acadêmica. Ele permite o cadastro e controle de usuários, gerenciamento de livros, registro de empréstimos e auditoria de atividades com criptografia de segurança.

---

## 🚀 Funcionalidades

- ✅ Autenticação de usuários com senhas criptografadas (`password_hash`)
- ✅ Perfis de acesso: administrador e bibliotecário
- ✅ Cadastro, edição e exclusão de usuários
- ✅ Gerenciamento de livros disponíveis para empréstimo
- ✅ Registro de empréstimos com controle por usuário
- ✅ Logs de atividades criptografados com AES-256
- ✅ Interface amigável com navegação entre páginas
- ✅ Proteção contra acesso não autorizado

---

## 🛠️ Tecnologias Utilizadas

| Tecnologia | Finalidade |
|------------|------------|
| PHP 8.3    | Backend e lógica do sistema |
| MySQL      | Banco de dados relacional |
| Bootstrap  | Estilização da interface |
| OpenSSL    | Criptografia AES-256-CBC |
| HTML/CSS   | Estrutura e layout |
| JavaScript | Interações básicas |

---

## 🔐 Segurança

- Senhas dos usuários são armazenadas com `password_hash()` e verificadas com `password_verify()`.
- Logs de auditoria são criptografados com AES-256-CBC usando a biblioteca OpenSSL.
- Acesso às páginas administrativas é restrito ao perfil de administrador.
- IPs e descrições de eventos são registrados e protegidos contra leitura direta.

---

## 📷 Evidências

As evidências do sistema estão disponíveis na pasta `/docs`, incluindo:
- Tela de login
- Cadastro de usuários
- Gerenciamento de livros
- Registro de empréstimos
- Tabela de auditoria antes e depois da criptografia

---

## 📦 Como Executar Localmente

1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-usuario/sistema-biblioteca-php.git
   
2. Importe o banco de dados Sistema de Gestão de Biblioteca.sql no MySQL.
3. Configure o arquivo conexao.php com suas credenciais:
   ```bash
   $conn = new mysqli("localhost", "usuario", "senha", "biblioteca");
4. Inicie o servidor local (XAMPP, WAMP ou PHP embutido).
5. Acesse http://localhost/nexus_library/index.php

👤 Autor
Bruno Neemias 

