<?php
session_start();

require_once("../../../assets/templates/header.php");

$conexao = conectar();

$idProfessor = $_SESSION['idUsuario'] ?? null;
$idTurma = filter_input(INPUT_GET, 'idTurma', FILTER_VALIDATE_INT);
$idDiciplina = filter_input(INPUT_GET, 'idDiciplina', FILTER_VALIDATE_INT);

$mensagem = '';
$tipoMensagem = '';
$aulas = [];
$dadosProfessor = null;

/* Verificar se existe um professor conectado */
if ($idProfessor !== null) {
    $sqlProfessor = "SELECT idUsuario, tipoUser, idInstituicao
                     FROM usuarios
                     WHERE idUsuario = ? AND tipoUser = 'Professor'
                     LIMIT 1";

    $stmtProfessor = mysqli_prepare($conexao, $sqlProfessor);

    if ($stmtProfessor) {
        mysqli_stmt_bind_param($stmtProfessor, "i", $idProfessor);
        mysqli_stmt_execute($stmtProfessor);

        $resultadoProfessor = mysqli_stmt_get_result($stmtProfessor);
        $dadosProfessor = mysqli_fetch_assoc($resultadoProfessor) ?: null;

        mysqli_stmt_close($stmtProfessor);
    }
}

/* Buscar as aulas vinculadas ao professor */
if ($dadosProfessor !== null) {
    $sqlAulas = "SELECT
                    au.idAula,
                    au.titulo AS aula,
                    au.tema,
                    d.idDiciplina,
                    d.diciplina AS materia,
                    d.idTurma,
                    t.serie
                 FROM aulas au
                 INNER JOIN diciplinas d
                    ON d.idDiciplina = au.idDiciplina
                 INNER JOIN turmas t
                    ON t.idTurma = d.idTurma
                 WHERE d.idProfessor = ?";

    if ($idTurma !== null && $idTurma > 0) {
        $sqlAulas .= " AND d.idTurma = ?";
    }

    if ($idDiciplina !== null && $idDiciplina > 0) {
        $sqlAulas .= " AND d.idDiciplina = ?";
    }

    $sqlAulas .= " ORDER BY t.serie ASC, d.diciplina ASC, au.titulo ASC";

    $stmtAulas = mysqli_prepare($conexao, $sqlAulas);

    if ($stmtAulas) {
        if (
            $idTurma !== null && $idTurma > 0 &&
            $idDiciplina !== null && $idDiciplina > 0
        ) {
            mysqli_stmt_bind_param(
                $stmtAulas,
                "iii",
                $idProfessor,
                $idTurma,
                $idDiciplina
            );
        } elseif ($idTurma !== null && $idTurma > 0) {
            mysqli_stmt_bind_param(
                $stmtAulas,
                "ii",
                $idProfessor,
                $idTurma
            );
        } elseif ($idDiciplina !== null && $idDiciplina > 0) {
            mysqli_stmt_bind_param(
                $stmtAulas,
                "ii",
                $idProfessor,
                $idDiciplina
            );
        } else {
            mysqli_stmt_bind_param($stmtAulas, "i", $idProfessor);
        }

        mysqli_stmt_execute($stmtAulas);

        $resultadoAulas = mysqli_stmt_get_result($stmtAulas);

        while ($linha = mysqli_fetch_assoc($resultadoAulas)) {
            $aulas[] = $linha;
        }

        mysqli_stmt_close($stmtAulas);
    }
}

/* Processar o formulário */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAulaPost = filter_input(INPUT_POST, 'idAula', FILTER_VALIDATE_INT);

    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $tipo = trim($_POST['tipo'] ?? '');
    $dificuldade = trim($_POST['dificuldade'] ?? '');
    $prazo = trim($_POST['prazo'] ?? '');

    if ($dadosProfessor === null) {
        $mensagem = 'Apenas um professor conectado pode cadastrar atividades.';
        $tipoMensagem = 'erro';

    } elseif (
        !$idAulaPost ||
        $titulo === '' ||
        $descricao === '' ||
        $tipo === '' ||
        $dificuldade === '' ||
        $prazo === ''
    ) {
        $mensagem = 'Preencha todos os campos do formulário.';
        $tipoMensagem = 'erro';

    } elseif (mb_strlen($descricao) > 150) {
        $mensagem = 'A descrição deve ter no máximo 150 caracteres.';
        $tipoMensagem = 'erro';

    } else {
        $dataPrazo = DateTime::createFromFormat('!Y-m-d', $prazo);

        if (
            !$dataPrazo ||
            $dataPrazo->format('Y-m-d') !== $prazo
        ) {
            $mensagem = 'Informe um prazo válido.';
            $tipoMensagem = 'erro';

        } elseif ($prazo < date('Y-m-d')) {
            $mensagem = 'O prazo não pode ser uma data passada.';
            $tipoMensagem = 'erro';

        } else {
            /* Confirmar que a aula pertence ao professor */
            $sqlValidarAula = "SELECT au.idAula
                               FROM aulas au
                               INNER JOIN diciplinas d
                                   ON d.idDiciplina = au.idDiciplina
                               WHERE au.idAula = ?
                                 AND d.idProfessor = ?
                               LIMIT 1";

            $stmtValidarAula = mysqli_prepare(
                $conexao,
                $sqlValidarAula
            );

            $aulaPermitida = false;

            if ($stmtValidarAula) {
                mysqli_stmt_bind_param(
                    $stmtValidarAula,
                    "ii",
                    $idAulaPost,
                    $idProfessor
                );

                mysqli_stmt_execute($stmtValidarAula);

                $resultadoValidacao = mysqli_stmt_get_result(
                    $stmtValidarAula
                );

                $aulaPermitida =
                    mysqli_num_rows($resultadoValidacao) > 0;

                mysqli_stmt_close($stmtValidarAula);
            }

            if (!$aulaPermitida) {
                $mensagem = 'A aula selecionada não pertence às suas disciplinas.';
                $tipoMensagem = 'erro';

            } elseif (empty($dadosProfessor['idInstituicao'])) {
                $mensagem = 'Seu usuário não possui uma instituição vinculada.';
                $tipoMensagem = 'erro';

            } else {
                /*
                 * Conforme a estrutura informada do banco,
                 * idInteracao referencia instituicoes.idInstituicao.
                 */
                $idInteracao = (int) $dadosProfessor['idInstituicao'];
                $dataCriacao = date('Y-m-d');

                $sqlInserir = "INSERT INTO atividades
                                (
                                    idAula,
                                    titulo,
                                    descricao,
                                    tipo,
                                    dificuldade,
                                    prazo,
                                    dataCriacao,
                                    idInteracao
                                )
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

                $stmtInserir = mysqli_prepare(
                    $conexao,
                    $sqlInserir
                );

                if ($stmtInserir) {
                    mysqli_stmt_bind_param(
                        $stmtInserir,
                        "issssssi",
                        $idAulaPost,
                        $titulo,
                        $descricao,
                        $tipo,
                        $dificuldade,
                        $prazo,
                        $dataCriacao,
                        $idInteracao
                    );

                    if (mysqli_stmt_execute($stmtInserir)) {
                        mysqli_stmt_close($stmtInserir);

                        $urlRetorno = 'Atividade_professor.php';

                        if ($idTurma !== null && $idTurma > 0) {
                            $urlRetorno .= '?idTurma=' . $idTurma;
                        }

                        header('Location: ' . $urlRetorno);
                        exit;

                    } else {
                        $mensagem = 'Não foi possível cadastrar a atividade. Verifique os dados e a estrutura do banco.';
                        $tipoMensagem = 'erro';

                        mysqli_stmt_close($stmtInserir);
                    }

                } else {
                    $mensagem = 'Não foi possível preparar o cadastro da atividade.';
                    $tipoMensagem = 'erro';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nova atividade</title>

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

        .topo {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 38px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 20px;
            padding: 0 30px;
            background: #fff;
            z-index: 5;
        }

        .topo a {
            color: #8aaec6;
            text-decoration: none;
        }

        .conteudo {
            max-width: 760px;
            margin: 0 auto;
            padding: 65px 20px 35px;
        }

        h1 {
            text-align: center;
            color: #c95176;
            margin: 0 0 8px;
            font-size: 34px;
        }

        .subtitulo {
            text-align: center;
            margin: 0 0 24px;
        }

        .formulario {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
        }

        label {
            display: block;
            margin: 14px 0 6px;
            font-weight: bold;
            font-size: 14px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font: inherit;
            background: #fff;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .botoes {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 22px;
        }

        .botao {
            display: inline-block;
            padding: 11px 16px;
            border: 0;
            border-radius: 6px;
            background: #c95176;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .botao-secundario {
            background: #c5e0e1;
            color: #222;
        }

        .mensagem {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .erro {
            background: #ffe1e1;
            color: #8b1e1e;
        }

        .aviso {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
            line-height: 1.5;
        }

        @media (max-width: 520px) {
            .conteudo {
                padding: 55px 12px 20px;
            }

            .formulario {
                padding: 17px;
            }

            h1 {
                font-size: 28px;
            }

            .topo {
                padding: 0 12px;
            }
        }
    </style>
</head>

<body>

    <header class="topo">
        <a href="Turmas_professor.php">Turmas</a>
        <a href="Atividade_professor.php">Atividades</a>
        <a href="index.php">Home</a>
    </header>

    <main class="conteudo">

        <h1>Nova atividade</h1>

        <p class="subtitulo">
            Preencha os dados para publicar uma atividade para seus alunos.
        </p>

        <?php if ($idProfessor === null || $dadosProfessor === null): ?>

            <div class="mensagem erro">
                Não foi possível identificar um professor conectado.
                Confira a sessão de login.
            </div>

        <?php elseif (count($aulas) === 0): ?>

            <div class="mensagem erro">
                Não há aulas disponíveis para suas disciplinas.
                Cadastre uma aula antes de criar a atividade.
            </div>

        <?php else: ?>

            <?php if ($mensagem !== ''): ?>
                <div class="mensagem <?= $tipoMensagem === 'erro' ? 'erro' : '' ?>">
                    <?= htmlspecialchars(
                        $mensagem,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </div>
            <?php endif; ?>

            <form class="formulario" method="POST" action="">

                <label for="idAula">Aula / matéria</label>

                <select name="idAula" id="idAula" required>
                    <option value="">Selecione uma aula</option>

                    <?php foreach ($aulas as $aula): ?>
                        <option
                            value="<?= (int) $aula['idAula'] ?>"
                            <?= (
                                isset($idAulaPost) &&
                                (int) $idAulaPost === (int) $aula['idAula']
                            ) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars(
                                $aula['materia'] . ' — ' .
                                $aula['serie'] . ' — ' .
                                $aula['aula'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>
                    <?php endforeach; ?>

                </select>

                <label for="titulo">Título da atividade</label>

                <input
                    type="text"
                    id="titulo"
                    name="titulo"
                    maxlength="255"
                    value="<?= htmlspecialchars(
                        $_POST['titulo'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

                <label for="descricao">
                    Descrição (máximo 150 caracteres)
                </label>

                <textarea
                    id="descricao"
                    name="descricao"
                    maxlength="150"
                    required
                ><?= htmlspecialchars(
                    $_POST['descricao'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                ) ?></textarea>

                <label for="tipo">Tipo de atividade</label>

                <select id="tipo" name="tipo" required>
                    <option value="">Selecione o tipo</option>

                    <?php
                    $tipos = [
                        'Trabalho',
                        'Exercício',
                        'Pesquisa',
                        'Leitura',
                        'Questionário',
                        'Outro'
                    ];

                    foreach ($tipos as $opcaoTipo):
                    ?>
                        <option
                            value="<?= htmlspecialchars(
                                $opcaoTipo,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            <?= (
                                ($_POST['tipo'] ?? '') === $opcaoTipo
                            ) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars(
                                $opcaoTipo,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>
                    <?php endforeach; ?>

                </select>

                <label for="dificuldade">Dificuldade</label>

                <select
                    id="dificuldade"
                    name="dificuldade"
                    required
                >
                    <option value="">Selecione a dificuldade</option>

                    <?php
                    $dificuldades = [
                        'Fácil',
                        'Intermediária',
                        'Difícil'
                    ];

                    foreach ($dificuldades as $opcaoDificuldade):
                    ?>
                        <option
                            value="<?= htmlspecialchars(
                                $opcaoDificuldade,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            <?= (
                                ($_POST['dificuldade'] ?? '') ===
                                $opcaoDificuldade
                            ) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars(
                                $opcaoDificuldade,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>
                    <?php endforeach; ?>

                </select>

                <label for="prazo">Prazo de entrega</label>

                <input
                    type="date"
                    id="prazo"
                    name="prazo"
                    min="<?= date('Y-m-d') ?>"
                    value="<?= htmlspecialchars(
                        $_POST['prazo'] ?? '',
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    required
                >

                <div class="botoes">
                    <button class="botao" type="submit">
                        Publicar atividade
                    </button>

                    <a
                        class="botao botao-secundario"
                        href="Turmas_professor.php"
                    >
                        Cancelar
                    </a>
                </div>

                <p class="aviso">
                    A data de criação será preenchida automaticamente
                    no momento da publicação.
                </p>

            </form>

        <?php endif; ?>

    </main>

</body>
</html>