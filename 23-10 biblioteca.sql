-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/10/2026 às 17:19
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `biblioteca`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `turma` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `nome`, `turma`) VALUES
(1, 'Ana Souza', '2A'),
(2, 'Bruno Lima', '2A'),
(3, 'Carla Mendes', '2B'),
(4, 'Diego Rocha', '2B'),
(5, 'Eduarda Alves', '3A'),
(6, 'Felipe Costa', '3A'),
(7, 'Gabriela Nunes', '3B'),
(8, 'Henrique Dias', '3B');

-- --------------------------------------------------------

--
-- Estrutura para tabela `emprestimos`
--

CREATE TABLE `emprestimos` (
  `id` int(11) NOT NULL,
  `livro_id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `data_emprestimo` date NOT NULL,
  `data_devolucao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `emprestimos`
--

INSERT INTO `emprestimos` (`id`, `livro_id`, `aluno_id`, `data_emprestimo`, `data_devolucao`) VALUES
(1, 1, 1, '2026-09-01', '2026-09-15'),
(2, 6, 2, '2026-09-10', NULL),
(3, 1, 3, '2026-09-20', NULL),
(4, 9, 5, '2026-09-22', NULL),
(5, 7, 4, '2026-09-05', '2026-09-19'),
(6, 11, 7, '2026-10-01', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `livros`
--

CREATE TABLE `livros` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `autor` varchar(100) NOT NULL,
  `ano` int(11) DEFAULT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 1,
  `capa` varchar(255) DEFAULT 'sem-capa.svg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `livros`
--

INSERT INTO `livros` (`id`, `titulo`, `autor`, `ano`, `genero`, `quantidade`, `capa`) VALUES
(1, 'Dom Casmurro', 'Machado de Assis', 1899, 'Romance', 3, 'capas/dom-casmurro.jpg'),
(2, 'Memórias Póstumas de Brás Cubas', 'Machado de Assis', 1881, 'Romance', 2, 'capas/memórias-postumas.jpg'),
(3, 'O Alienista', 'Machado de Assis', 1882, 'Conto', 2, 'capas/o-alienista.jpg'),
(4, 'O Cortiço', 'Aluísio Azevedo', 1890, 'Romance', 2, 'capas/o-cortiço.jpg'),
(5, 'Iracema', 'José de Alencar', 1865, 'Romance', 1, 'capas/iracema.jpg'),
(6, 'Vidas Secas', 'Graciliano Ramos', 1938, 'Romance', 2, 'capas/vidas-secas.jpg'),
(7, 'Capitães da Areia', 'Jorge Amado', 1937, 'Romance', 3, 'capas/capitães-de-areia.jpg'),
(8, 'A Hora da Estrela', 'Clarice Lispector', 1977, 'Romance', 1, 'capas/a-hora-da-estrela.jpg'),
(9, 'Quarto de Despejo', 'Carolina Maria de Jesus', 1960, 'Diário', 2, 'capas/quarto-de-despejo.jpg'),
(10, 'Grande Sertão: Veredas', 'João Guimarães Rosa', 1956, 'Romance', 1, 'capas/grande-sertão.jpg'),
(11, 'Auto da Compadecida', 'Ariano Suassuna', 1955, 'Teatro', 2, 'capas/auto-da-compadecida.jpg'),
(12, 'A Moreninha', 'Joaquim Manuel de Macedo', 1844, 'Romance', 1, 'capas/a-moreninha.jpg'),
(13, 'Ayashimon', 'Yuji Kaku', 2021, 'ação', 1, 'capas/13.jpg'),
(14, 'O gato preto', 'Edgar allan Poe', 1843, 'Terror', 1, 'capas/14.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `usuario`, `email`, `senha`, `data_cadastro`) VALUES
(1, '', 'alexandre', '', '$2y$10$Rdzv6T0mNMyosIbOKeYEfeq0SS9hlgH4d/EyKnMLi4.HHziN5csnu', '2026-10-21 14:23:27'),
(7, '', 'aaaaaa', '', '$2y$10$V7B/wROP0dloI4KHeWx5t.Am9iUXa1mafFE2nK6jmncpZBQsyNdoK', '2026-10-23 14:41:06'),
(8, '', 'luis', '', '$2y$10$LleeGtVLCIyvuB3N2tgdw.bNKC/H0xqJhFTtW1jowY9FZNNU93gTa', '2026-10-23 14:42:07');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `livro_id` (`livro_id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices de tabela `livros`
--
ALTER TABLE `livros`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `emprestimos`
--
ALTER TABLE `emprestimos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `livros`
--
ALTER TABLE `livros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `emprestimos`
--
ALTER TABLE `emprestimos`
  ADD CONSTRAINT `emprestimos_ibfk_1` FOREIGN KEY (`livro_id`) REFERENCES `livros` (`id`),
  ADD CONSTRAINT `emprestimos_ibfk_2` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
