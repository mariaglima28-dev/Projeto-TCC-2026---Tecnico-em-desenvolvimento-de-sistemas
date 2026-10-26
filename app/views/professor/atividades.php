<?php
session_start();

require_once("../../../assets/templates/header.php");

$conexao = conectar();

$idProfessor = $_SESSION['idUsuario'] ?? null;
$idTurma = filter_input(INPUT_GET, 'idTurma', FILTER_VALIDATE_INT);

$turmas = [];
$atividades = [];
$turmaSelecionada = null;
$turmaInvalida = false;

/* Buscar as turmas vinculadas ao professor */
if ($idProfessor !== null) {

    $sqlTurmas = "SELECT DISTINCT
                    t.idTurma,
                    t.serie
                  FROM turmas t
                  INNER JOIN disciplinas d
                    ON d.idTurma = t.idTurma
                  WHERE d.idProfessor = ?
                  ORDER BY t.serie ASC";

    $stmtTurmas = mysqli_prepare($conexao, $sqlTurmas);

    if ($stmtTurmas) {
        mysqli_stmt_bind_param(
            $stmtTurmas,
            "i",
            $idProfessor
        );

        mysqli_stmt_execute($stmtTurmas);

        $resultadoTurmas = mysqli_stmt_get_result($stmtTurmas);

        while ($turma = mysqli_fetch_assoc($resultadoTurmas)) {
            $turmas[] = $turma;
        }

        mysqli_stmt_close($stmtTurmas);
    }

    /* Verificar se a turma selecionada pertence ao professor */
    if ($idTurma !== null && $idTurma > 0) {

        foreach ($turmas as $turma) {
            if ((int) $turma['idTurma'] === $idTurma) {
                $turmaSelecionada = $turma;
                break;
            }
        }

        if ($turmaSelecionada === null) {
            $turmaInvalida = true;
        }
    }

    /*
     * Buscar atividades.
     * Quando uma turma é selecionada, mostrar somente
     * as atividades daquela turma.
     */
    if (!$turmaInvalida) {

        if ($turmaSelecionada !== null) {

            $sqlAtividades = "SELECT
                                a.idAtividade,
                                a.titulo,
                                a.descricao,
                                a.dataCriacao,
                                a.prazo,
                                au.titulo AS aula,
                                d.disciplina AS materia,
                                d.idTurma,
                                t.serie
                              FROM atividades a
                              INNER JOIN aulas au
                                ON au.idAula = a.idAula
                              INNER JOIN disciplinas d
                                ON d.idDisciplina = au.idDisciplina
                              INNER JOIN turmas t
                                ON t.idTurma = d.idTurma
                              WHERE d.idProfessor = ?
                                AND d.idTurma = ?
                              ORDER BY a.dataCriacao DESC,
                                       a.idAtividade DESC";

            $stmtAtividades = mysqli_prepare(
                $conexao,
                $sqlAtividades
            );

            if ($stmtAtividades) {
                mysqli_stmt_bind_param(
                    $stmtAtividades,
                    "ii",
                    $idProfessor,
                    $idTurma
                );
            }

        } else {

            /* Sem turma selecionada, mostrar todas do professor */
            $sqlAtividades = "SELECT
                                a.idAtividade,
                                a.titulo,
                                a.descricao,
                                a.dataCriacao,
                                a.prazo,
                                au.titulo AS aula,
                                d.disciplina AS materia,
                                d.idTurma,
                                t.serie
                              FROM atividades a
                              INNER JOIN aulas au
                                ON au.idAula = a.idAula
                              INNER JOIN disciplinas d
                                ON d.idDisciplina = au.idDisciplina
                              INNER JOIN turmas t
                                ON t.idTurma = d.idTurma
                              WHERE d.idProfessor = ?
                              ORDER BY a.dataCriacao DESC,
                                       a.idAtividade DESC";

            $stmtAtividades = mysqli_prepare(
                $conexao,
                $sqlAtividades
            );

            if ($stmtAtividades) {
                mysqli_stmt_bind_param(
                    $stmtAtividades,
                    "i",
                    $idProfessor
                );
            }
        }

        if (isset($stmtAtividades) && $stmtAtividades) {

            mysqli_stmt_execute($stmtAtividades);

            $resultadoAtividades = mysqli_stmt_get_result(
                $stmtAtividades
            );

            while ($atividade = mysqli_fetch_assoc($resultadoAtividades)) {
                $atividades[] = $atividade;
            }

            mysqli_stmt_close($stmtAtividades);
        }
    }
}

/* Formatar datas, inclusive quando o banco contém horário */
function formatarData($data) {
    if (empty($data) || $data === '0000-00-00') {
        return 'Não informada';
    }

    try {
        $dataFormatada = new DateTime($data);
        return $dataFormatada->format('d/m/Y');
    } catch (Exception $e) {
        return 'Não informada';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Atividades do Professor</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #ffd5b3;
        }

        .topo {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 35px;
            background-color: #fff;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 20px;
            padding: 0 30px;
            z-index: 10;
        }

        .topo a {
            color: #8aaec6;
            text-decoration: none;
        }

        .topo a:hover {
            color: #e6537b;
        }

        .menu {
            position: fixed;
            top: 35px;
            left: 0;
            width: 180px;
            height: calc(100vh - 35px);
            overflow-y: auto;
            background-color: #ffead9;
            border: 1px solid #168bff;
            padding: 10px 0;
        }

        .menu h3 {
            margin: 8px 13px;
            font-size: 17px;
        }

        .menu a {
            display: block;
            padding: 12px 13px;
            color: #000;
            text-decoration: none;
            font-size: 15px;
        }

        .menu a:hover,
        .menu a.selecionada {
            background-color: #ffd6b5;
            color: #e6537b;
        }

        .conteudo {
            margin-left: 180px;
            padding: 45px 15px 25px;
        }

        h1 {
            margin: 5px 0 20px;
            text-align: center;
            color: #e6537b;
            font-size: 34px;
        }

        .lista-atividades {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(200px, 1fr)
            );
            gap: 12px;
        }

        .atividade {
            overflow: hidden;
            border-radius: 15px;
            background-color: #fff;
        }

        .atividade-topo {
            padding: 12px 10px 8px;
            background-color: #c4e0e1;
        }

        .atividade h2 {
            margin: 0 0 8px;
            font-size: 18px;
            overflow-wrap: anywhere;
        }

        .atividade p {
            margin: 6px 0;
            font-size: 14px;
            overflow-wrap: anywhere;
        }

        .atividade-info {
            padding: 8px 10px 12px;
        }

        .mensagem {
            padding: 15px;
            background-color: #fff;
            border-radius: 10px;
            text-align: center;
        }

        .botao-voltar {
            display: inline-block;
            margin-bottom: 15px;
            color: #c95176;
            text-decoration: none;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            .menu {
                width: 145px;
            }

            .conteudo {
                margin-left: 145px;
                padding: 45px 10px 15px;
            }

            h1 {
                font-size: 25px;
            }

            .lista-atividades {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <header class="topo">
        <a href="Turmas_professor.php">Turmas</a>
        <a href="index.php">Home</a>
    </header>

    <nav class="menu">
        <h3>Turmas</h3>

        <?php if ($idProfessor === null): ?>

            <p style="padding: 0 13px;">
                Faça login para visualizar suas turmas.
            </p>

        <?php elseif (count($turmas) > 0): ?>

            <?php foreach ($turmas as $turma): ?>

                <a
                    class="<?= (
                        $turmaSelecionada !== null &&
                        (int) $turmaSelecionada['idTurma'] ===
                        (int) $turma['idTurma']
                    ) ? 'selecionada' : '' ?>"
                    href="atividades.php?idTurma=<?= (int) $turma['idTurma'] ?>"
                >
                    <?= htmlspecialchars(
                        $turma['serie'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </a>

            <?php endforeach; ?>

        <?php else: ?>

            <p style="padding: 0 13px;">
                Nenhuma turma vinculada.
            </p>

        <?php endif; ?>
    </nav>

    <main class="conteudo">

        <?php if ($idProfessor === null): ?>

            <p class="mensagem">
                Não foi possível identificar o professor conectado.
                Verifique o nome da variável de sessão utilizada no login.
            </p>

        <?php elseif ($turmaInvalida): ?>

            <h1>Turma não encontrada</h1>

            <p class="mensagem">
                Esta turma não está vinculada ao seu usuário.
                Selecione uma turma no menu lateral.
            </p>

        <?php else: ?>

            <?php if ($turmaSelecionada !== null): ?>

                <a class="botao-voltar" href="turmas.php">
                    ← Voltar para as turmas
                </a>

                <h1>
                    Atividades - Turma
                    <?= htmlspecialchars(
                        $turmaSelecionada['serie'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </h1>

            <?php else: ?>

                <h1>Atividades postadas</h1>

            <?php endif; ?>

            <?php if (count($atividades) > 0): ?>

                <section class="lista-atividades">

                    <?php foreach ($atividades as $atividade): ?>

                        <article class="atividade">

                            <div class="atividade-topo">

                                <h2>
                                    <?= htmlspecialchars(
                                        $atividade['titulo'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </h2>

                                <p>
                                    <strong>Turma:</strong>
                                    <?= htmlspecialchars(
                                        $atividade['serie'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <p>
                                    <strong>Matéria:</strong>
                                    <?= htmlspecialchars(
                                        $atividade['materia'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                            </div>

                            <div class="atividade-info">

                                <p>
                                    <?= nl2br(htmlspecialchars(
                                        $atividade['descricao'] ?? '',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    )) ?>
                                </p>

                                <p>
                                    <strong>Aula:</strong>
                                    <?= htmlspecialchars(
                                        $atividade['aula'] ?? 'Não informada',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </p>

                                <p>
                                    <strong>Postada em:</strong>
                                    <?= formatarData(
                                        $atividade['dataCriacao']
                                    ) ?>
                                </p>

                                <p>
                                    <strong>Prazo:</strong>
                                    <?= formatarData(
                                        $atividade['prazo']
                                    ) ?>
                                </p>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </section>

            <?php else: ?>

                <p class="mensagem">
                    <?php if ($turmaSelecionada !== null): ?>
                        Não existem atividades postadas para esta turma.
                    <?php else: ?>
                        Nenhuma atividade foi postada para suas disciplinas.
                    <?php endif; ?>
                </p>

            <?php endif; ?>

        <?php endif; ?>

    </main>

</body>
</html>