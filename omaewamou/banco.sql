CREATE DATABASE IF NOT EXISTS `biblioteca` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `biblioteca`;

-- Criação da tabela de livros com a coluna capa
CREATE TABLE IF NOT EXISTS `livros` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `titulo` VARCHAR(255) NOT NULL,
  `autor` VARCHAR(255) NOT NULL,
  `ano` INT NOT NULL,
  `genero` VARCHAR(100) NOT NULL,
  `quantidade` INT NOT NULL,
  `capa` VARCHAR(255) DEFAULT 'img/sem-capa.svg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insere os livros padrão já apontando para as imagens da pasta capas
INSERT INTO `livros` (`id`, `titulo`, `autor`, `ano`, `genero`, `quantidade`, `capa`) VALUES
(1, 'A Hora da Estrela', 'Clarice Lispector', 1977, 'Romance', 1, 'capas/a-hora-da-estrela.jpg'),
(2, 'A Moreninha', 'Joaquim Manuel de Macedo', 1844, 'Romance', 1, 'capas/a-moreninha.jpg'),
(3, 'Auto da Compadecida', 'Ariano Suassuna', 1955, 'Teatro', 2, 'capas/auto-da-compadecida.jpg'),
(4, 'Capitães da Areia', 'Jorge Amado', 1937, 'Romance', 3, 'capas/capitães-de-areia.jpg'),
(5, 'Dom Casmurro', 'Machado de Assis', 1899, 'Romance', 3, 'capas/dom-casmurro.jpg'),
(6, 'Grande Sertão: Veredas', 'João Guimarães Rosa', 1956, 'Romance', 1, 'capas/grande-sertão.jpg'),
(7, 'Iracema', 'José de Alencar', 1865, 'Romance', 1, 'capas/iracema.jpg'),
(8, 'Memórias Póstumas de Brás Cubas', 'Machado de Assis', 1881, 'Romance', 2, 'capas/memorias-postumas.jpg'),
(9, 'O Alienista', 'Machado de Assis', 1882, 'Conto', 2, 'capas/o-alienista.jpg'),
(10, 'O Cortiço', 'Aluísio Azevedo', 1890, 'Romance', 2, 'capas/o-cortiço.jpg'),
(11, 'Quarto de Despejo', 'Carolina Maria de Jesus', 1960, 'Diário', 2, 'capas/quarto-de-despejo.jpg'),
(12, 'Vidas Secas', 'Graciliano Ramos', 1938, 'Romance', 2, 'capas/vidas-secas.jpg');
