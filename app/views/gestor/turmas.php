<?php
session_start();
$tituloPagina='TURMAS';
require_once('../../../assets/templates/header.php');

$conexao = conectar();

// Verifica se o usuário é gestor
if (
    !isset($_SESSION['tipoUser']) ||
    $_SESSION['tipoUser'] !== 'Gestor' ||
    !isset($_SESSION['idInstituicao'])
) {
    header("Location: ../../../login.php");
    exit;
}

$idInstituicao = (int) $_SESSION['idInstituicao'];

// Busca as turmas e conta os alunos de cada uma
$sql = "
    SELECT
        t.idTurma,
        t.serie,
        COUNT(u.idUsuario) AS totalAlunos
    FROM turmas t
    LEFT JOIN usuarios u
        ON u.idTurma = t.idTurma
        AND u.idInstituicao = t.idInstituicao
        AND u.tipoUser = 'Aluno'
    WHERE t.idInstituicao = ?
    GROUP BY t.idTurma, t.serie
    ORDER BY t.serie
";

$stmt = mysqli_prepare($conexao, $sql);

if (!$stmt) {
    die("Erro ao preparar a consulta das turmas.");
}

mysqli_stmt_bind_param($stmt, "i", $idInstituicao);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
?>
<main class="conteudo">

    <h1>Turmas</h1>

    <?php if (isset($_GET['exclusao']) && $_GET['exclusao'] === 'sucesso'): ?>
        <div class="mensagem sucesso">
            Turma excluída com sucesso!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
        <div class="mensagem erro">
            <?= htmlspecialchars($_GET['erro'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
    <div class="area-cadastrar-turma"><a href="cadastroTurma.php" class="botao-cadastrar-turma">+ Cadastrar nova turma</a></div>
    <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
            <div class="mensagem sucesso">
                Turma cadastrada com sucesso!
            </div>
    <?php endif; ?>

    <section class="lista-turmas">

        <?php if (mysqli_num_rows($resultado) > 0): ?>

            <?php while ($turma = mysqli_fetch_assoc($resultado)): ?>

                <article class="card-turma">

                    <div class="cabecalho">
                        <h2>
                            <?= htmlspecialchars($turma['serie'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>

                        <span class="icone" aria-hidden="true">
                            ♧
                        </span>
                    </div>

                    <div class="detalhes">

                        <p>
                            <?= (int) $turma['totalAlunos'] ?>
                            aluno(s) matriculado(s)
                        </p>
                    </div>

                    <div class="acoes">

                        <a href="alunos.php?idTurma=<?= (int) $turma['idTurma'] ?>"class="botao botao-alunos">Ver Alunos</a>

                        <a href="editarTurma.php?idTurma=<?= (int) $turma['idTurma'] ?>"class="botao botao-editar" >Editar</a>

                        <?php if ((int) $turma['totalAlunos'] === 0): ?>
                            <a href="excluirTurma.php?idTurma=<?= (int) $turma['idTurma'] ?>" class="botao botao-excluir-link" onclick="return confirm('Tem certeza de que deseja excluir esta turma?');">Excluir</a>
                        <?php endif; ?>

                    </div>

                </article>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="mensagem">
                Nenhuma turma cadastrada nesta instituição.
            </div>

        <?php endif; ?>

    </section>
<?php
mysqli_stmt_close($stmt);
require_once __DIR__ . "/../../../assets/templates/footer.php";
?>