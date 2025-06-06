-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/04/2025 às 21:19
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
-- Banco de dados: `mesapro`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias_produto`
--

CREATE TABLE `categorias_produto` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categorias_produto`
--

INSERT INTO `categorias_produto` (`id`, `nome`) VALUES
(12, 'Águas'),
(1, 'Aperitivos'),
(6, 'Burgers'),
(2, 'Carnes'),
(14, 'Cerveja'),
(13, 'Chopps'),
(15, 'Destilados'),
(4, 'Frango'),
(3, 'Massas'),
(5, 'Peixe'),
(7, 'Saladas'),
(10, 'Sobremesa'),
(8, 'Sopas'),
(11, 'Sucos'),
(9, 'Vegetariano');

-- --------------------------------------------------------

--
-- Estrutura para tabela `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `funcao` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `employees`
--

INSERT INTO `employees` (`id`, `nome`, `cpf`, `telefone`, `email`, `senha`, `funcao`) VALUES
(13, 'Marcos Silva', '78357131956', '95983944347', 'marcos@email.com', '$2y$10$I/9rEVM6N4qMPgN.2BifO.ROT3qWpQOJsl3BYxoOG0uiJuTt1XH9y', 3),
(15, 'Renata Torres', '89695628931', '21989584038', 'renata@email.com', '$2y$10$Y0YyYI3MC.eQtyV4A5YAqeAyojA.Y46hoArjhubhFiiS0Xrt.KqY.', 2),
(16, 'Lucas Ribeiro', '85199926683', '14983884294', 'lucas@email.com', '$2y$10$AGzfi4rkW2Z9b2h/NXbqGeKwwtdpgqeoBwYyQspKWaNGS.A6I2AQq', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `preco` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `tipo_id`, `categoria_id`, `preco`) VALUES
(1, 'Cheeseburger Clássico', 1, 6, 29.99),
(2, 'Filé Mignon Grelhado', 1, 2, 59.99);

-- --------------------------------------------------------


CREATE TABLE `comandapedidos` (
  `id` int(11) NOT NULL,
  `produto_id` INT(11) NOT NULL
  `preco` decimal(10,2) NOT NULL,
  `quantidade` INT(10) NOT NULL,
  `info` varchar(100) NOT NULL,
  FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

- --------------------------------------------------------


-- --------------------------------------------------------



--
-- Estrutura para tabela `tables`
--

CREATE TABLE `tables` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `numero` tinyint(3) UNSIGNED NOT NULL,
  `capacidade` tinyint(3) UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL,
  `hora_reserva` datetime NOT NULL,
  `reservado_por` varchar(45) NOT NULL,
  `tel_reseva` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tables`
--

INSERT INTO `tables` (`id`, `numero`, `capacidade`, `status`, `hora_reserva`, `reservado_por`, `tel_reseva`) VALUES
(44, 1, 8, 0, '0000-00-00 00:00:00', '-', '-'),
(48, 3, 4, 2, '0000-00-00 00:00:00', '-', '-'),
(49, 4, 4, 1, '2025-04-15 01:06:00', 'Teste', '67123456789'),
(50, 5, 4, 1, '2025-04-15 16:15:00', 'Teste', '67123456789'),
(51, 6, 6, 0, '0000-00-00 00:00:00', '-', '-'),
(52, 2, 4, 0, '0000-00-00 00:00:00', '-', '-'),
(53, 7, 4, 0, '0000-00-00 00:00:00', '-', '-');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_produto`
--

CREATE TABLE `tipos_produto` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_produto`
--

INSERT INTO `tipos_produto` (`id`, `nome`) VALUES
(4, 'Bebidas alcoólicas'),
(3, 'Bebidas não alcoólicas'),
(2, 'Menu infantil'),
(1, 'Menu principal');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL DEFAULT '123456',
  `role` tinyint(3) UNSIGNED NOT NULL,
  `fullname` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `fullname`) VALUES
(1, 'marcos', 'marcos', 1, 'Marcos Silva'),
(2, 'renata', 'renata', 2, 'Renata Torres'),
(3, 'lucas', 'lucas', 3, 'Lucas Ribeiro');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias_produto`
--
ALTER TABLE `categorias_produto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipo_id` (`tipo_id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices de tabela `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`);

--
-- Índices de tabela `tipos_produto`
--
ALTER TABLE `tipos_produto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias_produto`
--
ALTER TABLE `categorias_produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tables`
--
ALTER TABLE `tables`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de tabela `tipos_produto`
--
ALTER TABLE `tipos_produto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--


ALTER TABLE `comandapedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`tipo_id`) REFERENCES `tipos_produto` (`id`),
  ADD CONSTRAINT `produtos_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_produto` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
