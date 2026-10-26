<?php
session_start();
$tutuloPagina='TURMAS';
require_once("../../../assets/templates/header.php");

$conexao = conectar();

$idProfessor = $_SESSION['idUsuario'] ?? null;
$turmas = [];

/* Buscar as turmas e disciplinas do professor */
if ($idProfessor !== null) {

    $sql = "SELECT DISTINCT
                t.idTurma,
                t.serie,
                d.idDisciplina,
                d.disciplina AS materia
            FROM disciplinas d
            INNER JOIN turmas t
                ON t.idTurma = d.idTurma
            WHERE d.idProfessor = ?
            ORDER BY t.serie ASC, d.disciplina ASC";

    $stmt = mysqli_prepare($conexao, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $idProfessor);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        while ($linha = mysqli_fetch_assoc($resultado)) {
            $turmas[] = $linha;
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title></title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #ffd5b3;
            color: #222;
        }

        
    </style>
</head>

<body>

    <!-- Menu superior -->
    <header class="topo">
        <a href="Quiz_professor.php">Quiz</a>
        <a href="atividades.php">Atividades</a>
        <a href="Desempenho_professor.php">Desempenho</a>
        <a href="IA.php">IA</a>
        <a href="index.php">Home</a>
    </header>

    <!-- Menu lateral -->
    <nav class="menu">
        <h2>Turmas</h2>

        <?php foreach ($turmas as $turma): ?>
            <a href="atividades.php?idTurma=<?= (int) $turma['idTurma'] ?>">
                <?= htmlspecialchars(
                    $turma['serie'],
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Conteúdo -->
    <main class="conteudo">

        <h1>Turmas</h1>

        <?php if ($idProfessor === null): ?>

            <p class="mensagem">
                Faça login para visualizar suas turmas.
            </p>

        <?php elseif (count($turmas) === 0): ?>

            <p class="mensagem">
                Nenhuma turma foi encontrada para o seu usuário.
            </p>

        <?php else: ?>

            <section class="lista-turmas">

                <?php foreach ($turmas as $turma): ?>

                    <article class="card-turma">

                        <!-- Clicar no cabeçalho ou nos detalhes abre as atividades -->
                        <a
                            class="card-link"
                            href="atividades.php?idTurma=<?= (int) $turma['idTurma'] ?>"
                        >

                            <div class="cabecalho">

                                <h2>
                                    <?= htmlspecialchars(
                                        $turma['materia'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h2>

                                <p>
                                    <?= htmlspecialchars(
                                        $turma['serie'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <span class="icone" aria-hidden="true">
                                    ♙
                                </span>

                            </div>

                            <div class="detalhes">
                                <p>
                                    <strong>Disciplina:</strong>
                                    <?= htmlspecialchars(
                                        $turma['materia'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <p>
                                    Clique para ver as atividades postadas.
                                </p>
                            </div>

                        </a>

                        <!-- Botões independentes do link principal -->
                        <div class="acoes">

                            <a
                                class="botao botao-atividades"
                                href="atividades.php?idTurma=<?= (int) $turma['idTurma'] ?>"
                            >
                                Ver atividades
                            </a>

                            <a
                                class="botao botao-postar"
                                href="novaAtividade.php?idTurma=<?= (int) $turma['idTurma'] ?>&idDisciplina=<?= (int) $turma['idDisciplina'] ?>"
                            >
                                + Postar atividade
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php endif; ?>

    </main>

</body>
</html>