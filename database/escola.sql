-- Criação do Banco de Dados
CREATE DATABASE IF NOT EXISTS escola;
USE escola;

-- 1. Tabela de Alunos (entidade principal de gestão)
-- Adicionamos campos para melhor identificação e ligamos a uma possível conta de usuário
CREATE TABLE alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricula VARCHAR(20) NOT NULL UNIQUE, -- Código único de matrícula
    nome VARCHAR(100) NOT NULL,
    data_nascimento DATE,
    cpf VARCHAR(14) UNIQUE, -- CPF do aluno ou responsável
    telefone VARCHAR(20),
    email VARCHAR(100) UNIQUE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    turma_id INT, -- Chave estrangeira para a tabela turmas
    usuario_id INT, -- Chave estrangeira para a tabela usuarios (opcional, para login)
    status ENUM('Ativo', 'Inativo', 'Trancado') DEFAULT 'Ativo'
);

-- 2. Tabela de Professores
CREATE TABLE professores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE,
    email VARCHAR(100) UNIQUE,
    telefone VARCHAR(20),
    usuario_id INT UNIQUE, -- Chave estrangeira para a tabela usuarios (obrigatório para login)
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabela de Usuários (para login de todos os atores: Admin, Secretário, Professor, Aluno)
-- Esta tabela é central para o caso de uso 'Login' e 'Gerenciar Usuários'
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE, -- Nome de usuário ou matrícula/CPF para login
    senha_hash VARCHAR(255) NOT NULL,    -- Senha criptografada (hash)
    tipo_usuario ENUM('Admin', 'Secretario', 'Professor', 'Aluno') NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Tabela de Turmas
CREATE TABLE turmas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_turma VARCHAR(50) NOT NULL UNIQUE, -- Ex: "8A - Manhã"
    ano_letivo YEAR NOT NULL,
    professor_responsavel_id INT, -- Chave estrangeira para a tabela professores
    status ENUM('Aberta', 'Fechada') DEFAULT 'Aberta'
);

-- 5. Tabela de Disciplinas (para gerenciar disciplinas - caso de uso do Secretário)
CREATE TABLE disciplinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    carga_horaria INT,
    professor_id INT -- Professor que leciona a disciplina
);

-- 6. Tabela de Mensalidades (para gerenciar mensalidades - caso de uso do Secretário)
CREATE TABLE mensalidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    data_vencimento DATE NOT NULL,
    data_pagamento DATE,
    status ENUM('Pendente', 'Pago', 'Atrasado') DEFAULT 'Pendente'
);

-- 7. Tabela de Eventos (para o caso de uso 'Consultar eventos' do Anônimo/Aluno)
CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    data_evento DATE NOT NULL,
    hora_inicio TIME,
    hora_fim TIME
);

-- Adicionando Chaves Estrangeiras (FKs) para garantir a integridade dos dados
-- FK Alunos -> Turmas
ALTER TABLE alunos
ADD CONSTRAINT fk_aluno_turma
FOREIGN KEY (turma_id) REFERENCES turmas(id)
ON DELETE SET NULL;

-- FK Alunos -> Usuarios
ALTER TABLE alunos
ADD CONSTRAINT fk_aluno_usuario
FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
ON DELETE SET NULL;

-- FK Professores -> Usuarios
ALTER TABLE professores
ADD CONSTRAINT fk_professor_usuario
FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
ON DELETE CASCADE; -- Se o usuário for excluído, o professor também é

-- FK Turmas -> Professores (Responsável)
ALTER TABLE turmas
ADD CONSTRAINT fk_turma_professor_responsavel
FOREIGN KEY (professor_responsavel_id) REFERENCES professores(id)
ON DELETE SET NULL;

-- FK Disciplinas -> Professores
ALTER TABLE disciplinas
ADD CONSTRAINT fk_disciplina_professor
FOREIGN KEY (professor_id) REFERENCES professores(id)
ON DELETE SET NULL;

-- FK Mensalidades -> Alunos
ALTER TABLE mensalidades
ADD CONSTRAINT fk_mensalidade_aluno
FOREIGN KEY (aluno_id) REFERENCES alunos(id)
ON DELETE CASCADE;

-- Exemplo de inserção para teste (CRIAR UM ADMINISTRADOR)
INSERT INTO usuarios (usuario, senha_hash, tipo_usuario) VALUES
('admin', '12345', 'Admin'),
('secretaria', '12345', 'Secretaria');