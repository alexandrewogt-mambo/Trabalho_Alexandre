-- Cria a tabela de usuários que podem entrar no sistema.
-- Como usar: abra o phpMyAdmin, clique na aba "SQL" e cole/execute este arquivo.
USE biblioteca;

CREATE TABLE IF NOT EXISTS usuarios (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50)  NOT NULL UNIQUE,
  senha   VARCHAR(255) NOT NULL   -- guarda o "hash" da senha, nunca a senha de verdade
);

-- Usuário inicial:  admin  /  senha: biblioteca123
-- (depois de entrar, use o menu "Trocar senha" para mudar)
INSERT INTO usuarios (usuario, senha) VALUES ('admin', '$2y$10$zmiO1SKUxtzuSzWnSL5gFehOLtmMkSoI/wX8JtFkp.fZoqs0MfoOO');
