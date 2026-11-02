-- =====================================================================
-- banco.sql - Script de criação do banco de dados
-- ---------------------------------------------------------------------
-- Rode este arquivo UMA única vez no phpMyAdmin (aba "SQL" ou "Importar").
--
-- Ele cria (se ainda não existir) o banco "todo_list" e as duas tabelas
-- usadas pelo projeto:
--     * usuarios -> usada pelo LOGIN  (UserController / model User)
--     * tarefas  -> usada pela TO-DO  (tarefaController / model Tarefa)
--
-- Observação: CREATE TABLE IF NOT EXISTS = "criar somente se não existir",
-- por isso rodar de novo não apaga nem altera nada que já esteja lá.
-- =====================================================================

-- Cria o banco de dados (se já existir, não faz nada)
CREATE DATABASE IF NOT EXISTS todo_list;

-- Seleciona o banco para os próximos comandos usarem nele
USE todo_list;

-- ---------------------------------------------------------------------
-- Tabela de USUÁRIOS - guardada no banco pelo login
-- ---------------------------------------------------------------------
-- id      -> número que identifica cada usuário (auto_increment = o próprio
--            MySQL gera sozinho: 1, 2, 3...)
-- nome    -> nome exibido na saudação "Olá, ..."
-- email   -> usado para localizar o usuário no login (UNIQUE = único,
--            não dá para cadastrar o mesmo e-mail duas vezes)
-- senha   -> guarda o HASH da senha (nunca a senha em texto puro).
--            VARCHAR(255) porque o hash gerado pelo password_hash()
--            ocupa 60 caracteres; reserva-se mais espaço.
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- ---------------------------------------------------------------------
-- Tabela de TAREFAS - guardada pela to-do list
-- ---------------------------------------------------------------------
-- id           -> identificador único da tarefa
-- descricao    -> texto da tarefa
-- concluida    -> 0 = pendente / 1 = concluída (padrão 0)
-- data_criacao -> data e hora automática no momento do INSERT; é por
--                 ela que a lista ordena (mais nova primeiro)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tarefas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(255) NOT NULL,
    concluida TINYINT(1) DEFAULT 0,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- Usuário de teste (para já dar para logar sem precisar cadastrar)
--   e-mail: admin@todo.com
--   senha : 123456
--
-- A coluna senha recebe o HASH da senha "123456", gerado com
-- password_hash($senha, PASSWORD_BCRYPT). No login, o userController
-- confere a senha digitada com password_verify() contra este hash.
-- ---------------------------------------------------------------------
INSERT INTO usuarios (nome, email, senha) VALUES
('Admin', 'admin@todo.com', '$2y$10$J.sYgIhWipgbKAR70p6/FOI4uBUeLXKGgy8RRLdh89xPe5B9mLbQq');
