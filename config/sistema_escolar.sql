-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 31/08/2026 às 22:16
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
-- Banco de dados: `sistema_escolar`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alternativas`
--

CREATE TABLE `alternativas` (
  `idAltenativa` int(11) NOT NULL,
  `idQuestao` int(11) NOT NULL,
  `texto` varchar(255) NOT NULL,
  `correta` enum('Correta','Errada') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividade`
--

CREATE TABLE `atividade` (
  `idAtividade` int(11) NOT NULL,
  `idAula` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` varchar(150) NOT NULL,
  `tipo` text NOT NULL,
  `dificuldade` varchar(255) NOT NULL,
  `prazo` date NOT NULL,
  `dataCriacao` date NOT NULL,
  `idInteracao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aula`
--

CREATE TABLE `aula` (
  `idAula` int(11) NOT NULL,
  `idDiciplina` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tema` varchar(255) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `bncc` varchar(255) NOT NULL,
  `dataAula` date NOT NULL,
  `dataCriacao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conquista`
--

CREATE TABLE `conquista` (
  `idConquista` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `criterios` varchar(255) NOT NULL,
  `icone` blob NOT NULL,
  `descricao` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conquista_aluno`
--

CREATE TABLE `conquista_aluno` (
  `idConquistaAluno` int(11) NOT NULL,
  `idConquista` int(11) NOT NULL,
  `idAluno` int(11) NOT NULL,
  `dataConquista` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `correcao`
--

CREATE TABLE `correcao` (
  `idCorrecao` int(11) NOT NULL,
  `idResposta` int(11) NOT NULL,
  `nota` int(4) NOT NULL,
  `acertos` varchar(255) NOT NULL,
  `erros` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `diciplina`
--

CREATE TABLE `diciplina` (
  `idDiciplina` int(11) NOT NULL,
  `diciplina` varchar(255) NOT NULL,
  `idProfessor` int(11) NOT NULL,
  `idTurma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `instituicao`
--

CREATE TABLE `instituicao` (
  `idInstituicao` int(11) NOT NULL,
  `cnpj` varchar(14) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `material`
--

CREATE TABLE `material` (
  `idMaterial` int(11) NOT NULL,
  `idAula` int(11) NOT NULL,
  `idAtividade` int(11) NOT NULL,
  `materialUrl` varchar(255) NOT NULL,
  `titulo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil_aprendizagem`
--

CREATE TABLE `perfil_aprendizagem` (
  `idPerfil` int(11) NOT NULL,
  `idAluno` int(11) NOT NULL,
  `nivelAprendizagem` varchar(255) NOT NULL,
  `dificuldades` varchar(255) NOT NULL,
  `preferencias` varchar(255) NOT NULL,
  `observacao` varchar(255) NOT NULL,
  `dataAtualizacao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `questoes`
--

CREATE TABLE `questoes` (
  `idQuestao` int(11) NOT NULL,
  `idQuiz` int(11) NOT NULL,
  `enunciado` varchar(255) NOT NULL,
  `tipo` enum('multiplas escolhas','verdadeiro ou falso','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `quiz`
--

CREATE TABLE `quiz` (
  `idQuiz` int(11) NOT NULL,
  `idAula` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `objetivo` varchar(255) NOT NULL,
  `nivelInical` varchar(255) NOT NULL,
  `inicio` datetime NOT NULL,
  `fim` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `resposta`
--

CREATE TABLE `resposta` (
  `idResposta` int(11) NOT NULL,
  `idAluno` int(11) NOT NULL,
  `idAtividade` int(11) NOT NULL,
  `idQuestao` int(11) NOT NULL,
  `dataEnvio` date NOT NULL,
  `respostaUrl` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `turma`
--

CREATE TABLE `turma` (
  `idTurma` int(11) NOT NULL,
  `serie` varchar(255) NOT NULL,
  `idInstituicao` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `IdGoogle` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `dataNascimento` date NOT NULL,
  `tipoUser` enum('Aluno','Professor','Gestor') NOT NULL DEFAULT 'Aluno',
  `idTurma` int(11) NOT NULL,
  `idInstituicao` int(11) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `foto` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alternativas`
--
ALTER TABLE `alternativas`
  ADD PRIMARY KEY (`idAltenativa`),
  ADD KEY `idQuestao` (`idQuestao`);

--
-- Índices de tabela `atividade`
--
ALTER TABLE `atividade`
  ADD PRIMARY KEY (`idAtividade`),
  ADD KEY `idAula` (`idAula`),
  ADD KEY `idInteracao` (`idInteracao`);

--
-- Índices de tabela `aula`
--
ALTER TABLE `aula`
  ADD PRIMARY KEY (`idAula`),
  ADD KEY `idDiciplina` (`idDiciplina`);

--
-- Índices de tabela `conquista`
--
ALTER TABLE `conquista`
  ADD PRIMARY KEY (`idConquista`);

--
-- Índices de tabela `conquista_aluno`
--
ALTER TABLE `conquista_aluno`
  ADD PRIMARY KEY (`idConquistaAluno`),
  ADD KEY `idAluno` (`idAluno`),
  ADD KEY `idConquista` (`idConquista`);

--
-- Índices de tabela `correcao`
--
ALTER TABLE `correcao`
  ADD PRIMARY KEY (`idCorrecao`),
  ADD KEY `idResposta` (`idResposta`);

--
-- Índices de tabela `diciplina`
--
ALTER TABLE `diciplina`
  ADD PRIMARY KEY (`idDiciplina`),
  ADD KEY `idProfessor` (`idProfessor`),
  ADD KEY `idTurma` (`idTurma`);

--
-- Índices de tabela `instituicao`
--
ALTER TABLE `instituicao`
  ADD PRIMARY KEY (`idInstituicao`);

--
-- Índices de tabela `material`
--
ALTER TABLE `material`
  ADD PRIMARY KEY (`idMaterial`),
  ADD KEY `idAula` (`idAula`),
  ADD KEY `idAtividade` (`idAtividade`);

--
-- Índices de tabela `perfil_aprendizagem`
--
ALTER TABLE `perfil_aprendizagem`
  ADD PRIMARY KEY (`idPerfil`),
  ADD KEY `idAluno` (`idAluno`);

--
-- Índices de tabela `questoes`
--
ALTER TABLE `questoes`
  ADD PRIMARY KEY (`idQuestao`),
  ADD KEY `idQuiz` (`idQuiz`);

--
-- Índices de tabela `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`idQuiz`),
  ADD KEY `idAula` (`idAula`);

--
-- Índices de tabela `resposta`
--
ALTER TABLE `resposta`
  ADD PRIMARY KEY (`idResposta`),
  ADD KEY `idAluno` (`idAluno`),
  ADD KEY `idAtividade` (`idAtividade`),
  ADD KEY `idQuestao` (`idQuestao`);

--
-- Índices de tabela `turma`
--
ALTER TABLE `turma`
  ADD PRIMARY KEY (`idTurma`),
  ADD KEY `idInstituicao` (`idInstituicao`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`),
  ADD KEY `idTurma` (`idTurma`),
  ADD KEY `idInstituicao` (`idInstituicao`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alternativas`
--
ALTER TABLE `alternativas`
  MODIFY `idAltenativa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `atividade`
--
ALTER TABLE `atividade`
  MODIFY `idAtividade` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aula`
--
ALTER TABLE `aula`
  MODIFY `idAula` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conquista`
--
ALTER TABLE `conquista`
  MODIFY `idConquista` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conquista_aluno`
--
ALTER TABLE `conquista_aluno`
  MODIFY `idConquistaAluno` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `correcao`
--
ALTER TABLE `correcao`
  MODIFY `idCorrecao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `diciplina`
--
ALTER TABLE `diciplina`
  MODIFY `idDiciplina` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `instituicao`
--
ALTER TABLE `instituicao`
  MODIFY `idInstituicao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `material`
--
ALTER TABLE `material`
  MODIFY `idMaterial` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `perfil_aprendizagem`
--
ALTER TABLE `perfil_aprendizagem`
  MODIFY `idPerfil` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `questoes`
--
ALTER TABLE `questoes`
  MODIFY `idQuestao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `quiz`
--
ALTER TABLE `quiz`
  MODIFY `idQuiz` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `resposta`
--
ALTER TABLE `resposta`
  MODIFY `idResposta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `turma`
--
ALTER TABLE `turma`
  MODIFY `idTurma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `alternativas`
--
ALTER TABLE `alternativas`
  ADD CONSTRAINT `alternativas_ibfk_1` FOREIGN KEY (`idQuestao`) REFERENCES `questoes` (`idQuestao`);

--
-- Restrições para tabelas `atividade`
--
ALTER TABLE `atividade`
  ADD CONSTRAINT `atividade_ibfk_1` FOREIGN KEY (`idAula`) REFERENCES `aula` (`idAula`),
  ADD CONSTRAINT `atividade_ibfk_2` FOREIGN KEY (`idInteracao`) REFERENCES `instituicao` (`idInstituicao`);

--
-- Restrições para tabelas `aula`
--
ALTER TABLE `aula`
  ADD CONSTRAINT `aula_ibfk_1` FOREIGN KEY (`idDiciplina`) REFERENCES `diciplina` (`idDiciplina`);

--
-- Restrições para tabelas `conquista_aluno`
--
ALTER TABLE `conquista_aluno`
  ADD CONSTRAINT `conquista_aluno_ibfk_1` FOREIGN KEY (`idAluno`) REFERENCES `usuarios` (`idUsuario`),
  ADD CONSTRAINT `conquista_aluno_ibfk_2` FOREIGN KEY (`idConquista`) REFERENCES `conquista` (`idConquista`);

--
-- Restrições para tabelas `correcao`
--
ALTER TABLE `correcao`
  ADD CONSTRAINT `correcao_ibfk_1` FOREIGN KEY (`idResposta`) REFERENCES `resposta` (`idResposta`);

--
-- Restrições para tabelas `diciplina`
--
ALTER TABLE `diciplina`
  ADD CONSTRAINT `diciplina_ibfk_1` FOREIGN KEY (`idProfessor`) REFERENCES `usuarios` (`idUsuario`),
  ADD CONSTRAINT `diciplina_ibfk_2` FOREIGN KEY (`idTurma`) REFERENCES `turma` (`idTurma`);

--
-- Restrições para tabelas `material`
--
ALTER TABLE `material`
  ADD CONSTRAINT `material_ibfk_1` FOREIGN KEY (`idAula`) REFERENCES `aula` (`idAula`),
  ADD CONSTRAINT `material_ibfk_2` FOREIGN KEY (`idAtividade`) REFERENCES `atividade` (`idAtividade`);

--
-- Restrições para tabelas `perfil_aprendizagem`
--
ALTER TABLE `perfil_aprendizagem`
  ADD CONSTRAINT `perfil_aprendizagem_ibfk_1` FOREIGN KEY (`idAluno`) REFERENCES `usuarios` (`idUsuario`);

--
-- Restrições para tabelas `questoes`
--
ALTER TABLE `questoes`
  ADD CONSTRAINT `questoes_ibfk_1` FOREIGN KEY (`idQuiz`) REFERENCES `quiz` (`idQuiz`);

--
-- Restrições para tabelas `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `quiz_ibfk_1` FOREIGN KEY (`idAula`) REFERENCES `aula` (`idAula`);

--
-- Restrições para tabelas `resposta`
--
ALTER TABLE `resposta`
  ADD CONSTRAINT `resposta_ibfk_1` FOREIGN KEY (`idAluno`) REFERENCES `usuarios` (`idUsuario`),
  ADD CONSTRAINT `resposta_ibfk_2` FOREIGN KEY (`idAtividade`) REFERENCES `atividade` (`idAtividade`),
  ADD CONSTRAINT `resposta_ibfk_3` FOREIGN KEY (`idQuestao`) REFERENCES `questoes` (`idQuestao`);

--
-- Restrições para tabelas `turma`
--
ALTER TABLE `turma`
  ADD CONSTRAINT `turma_ibfk_1` FOREIGN KEY (`idInstituicao`) REFERENCES `instituicao` (`idInstituicao`);

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`idTurma`) REFERENCES `turma` (`idTurma`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`idInstituicao`) REFERENCES `instituicao` (`idInstituicao`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
