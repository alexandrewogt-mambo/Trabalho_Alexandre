-- Cria o banco e as tabelas do sistema
CREATE DATABASE IF NOT EXISTS biblioteca
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE biblioteca;

-- Tabela de livros
CREATE TABLE IF NOT EXISTS livros (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(150) NOT NULL,
  autor VARCHAR(100) NOT NULL,
  ano INT NULL,
  genero VARCHAR(50) NULL,
  quantidade INT NOT NULL DEFAULT 1
);

-- Tabela de alunos
CREATE TABLE IF NOT EXISTS alunos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  turma VARCHAR(50) NOT NULL
);

-- Tabela de empréstimos
CREATE TABLE IF NOT EXISTS emprestimos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  livro_id INT NOT NULL,
  aluno_id INT NOT NULL,
  data_emprestimo DATE NOT NULL,
  data_devolucao DATE NULL,
  FOREIGN KEY (livro_id) REFERENCES livros(id),
  FOREIGN KEY (aluno_id) REFERENCES alunos(id)
);

-- Tabela de usuários do sistema
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL
);

-- Usuário inicial: admin / biblioteca123
INSERT INTO usuarios (usuario, senha)
VALUES ('admin', '$2y$10$zmiO1SKUxtzuSzWnSL5gFehOLtmMkSoI/wX8JtFkp.fZoqs0MfoOO');

ALTER TABLE usuarios
  ADD COLUMN nome VARCHAR(100) NULL AFTER usuario,
  ADD COLUMN bio VARCHAR(255) NULL AFTER nome,
  ADD COLUMN foto VARCHAR(255) NULL AFTER bio,
  ADD COLUMN tema VARCHAR(20) NOT NULL DEFAULT 'claro' AFTER foto;