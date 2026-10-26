
<?php
session_start();

$tituloPagina = "ALUNOS";

require_once("../../../assets/templates/header.php");

$conexao = conectar();

/*
|--------------------------------------------------------------------------
| VERIFICAR ACESSO DO GESTOR
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION["idInstituicao"]) ||
    !isset($_SESSION["tipoUser"]) ||
    $_SESSION["tipoUser"] !== "Gestor"
) {
    echo "<p>Acesso não autorizado.</p>";
    exit;
}

$idInstituicao = (int) $_SESSION["idInstituicao"];

/*
|--------------------------------------------------------------------------
| VERIFICAR SE UMA TURMA FOI SELECIONADA
|--------------------------------------------------------------------------
*/

$idTurma = isset($_GET["idTurma"])
    ? (int) $_GET["idTurma"]
    : 0;

$turma = null;

/*
|--------------------------------------------------------------------------
| BUSCAR TURMA SE HOUVER FILTRO
|--------------------------------------------------------------------------
*/

if ($idTurma > 0) {

    $sqlTurma = "
        SELECT idTurma, serie
        FROM turmas
        WHERE idTurma = ?
        AND idInstituicao = ?
    ";

    $stmtTurma = mysqli_prepare($conexao, $sqlTurma);

    mysqli_stmt_bind_param(
        $stmtTurma,
        "ii",
        $idTurma,
        $idInstituicao
    );

    mysqli_stmt_execute($stmtTurma);

    $resultadoTurma = mysqli_stmt_get_result($stmtTurma);

    $turma = mysqli_fetch_assoc($resultadoTurma);

    mysqli_stmt_close($stmtTurma);

    if (!$turma) {
        echo "<p>Turma não encontrada.</p>";
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| BUSCAR ALUNOS E SUAS TURMAS
|--------------------------------------------------------------------------
*/

if ($idTurma > 0) {

    $sqlAlunos = "
        SELECT
            u.idUsuario,
            u.nome,
            u.email,
            u.dataNascimento,
            u.idTurma,
            t.serie
        FROM usuarios u
        LEFT JOIN turmas t
            ON u.idTurma = t.idTurma
            AND t.idInstituicao = u.idInstituicao
        WHERE u.tipoUser = 'Aluno'
        AND u.idInstituicao = ?
        AND u.idTurma = ?
        ORDER BY u.nome ASC
    ";

    $stmtAlunos = mysqli_prepare($conexao, $sqlAlunos);

    mysqli_stmt_bind_param(
        $stmtAlunos,
        "ii",
        $idInstituicao,
        $idTurma
    );

} else {

    $sqlAlunos = "
        SELECT
            u.idUsuario,
            u.nome,
            u.email,
            u.dataNascimento,
            u.idTurma,
            t.serie
        FROM usuarios u
        LEFT JOIN turmas t
            ON u.idTurma = t.idTurma
            AND t.idInstituicao = u.idInstituicao
        WHERE u.tipoUser = 'Aluno'
        AND u.idInstituicao = ?
        ORDER BY u.nome ASC
    ";

    $stmtAlunos = mysqli_prepare($conexao, $sqlAlunos);

    mysqli_stmt_bind_param(
        $stmtAlunos,
        "i",
        $idInstituicao
    );
}

mysqli_stmt_execute($stmtAlunos);

$resultadoAlunos = mysqli_stmt_get_result($stmtAlunos);

?>

<main class="pagina-alunos">

    <?php if($idTurma){?>
        <a href="turmas.php" class="botao-voltar"> ← Voltar para turmas</a>
    <?php } ?>

    <div class="cabecalho-alunos">

        <h1>
            <?php if ($turma): ?>
                Alunos - <?= htmlspecialchars(
                    $turma["serie"],
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            <?php else: ?>
                Alunos da instituição
            <?php endif; ?>
        </h1>

        <p>
            <?php if ($turma): ?>
                Alunos cadastrados nesta turma.
            <?php else: ?>
                Todos os alunos cadastrados na sua instituição.
            <?php endif; ?>
        </p>

    </div>

    <?php if (mysqli_num_rows($resultadoAlunos) > 0): ?>

        <div class="tabela-container">

            <table class="tabela-alunos">

                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                         <?php if(!$idTurma){?><th>Turma</th><?php }?>
                        <th>Data de nascimento</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($aluno = mysqli_fetch_assoc($resultadoAlunos)): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    $aluno["nome"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $aluno["email"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </td>

                            <?php if(!$idTurma){?>
                                <td>
                                    <?php echo htmlspecialchars(
                                    $aluno["serie"] ?? "Turma não encontrada",
                                    ENT_QUOTES,
                                    "UTF-8"
                                );
                                }?>
                            </td>

                            <td>
                                <?= htmlspecialchars(
                                    $aluno["dataNascimento"] ?? "",
                                    ENT_QUOTES,
                                    "UTF-8"
                                ) ?>
                            </td>

                            <td class="acoes-aluno">
                                <a href="editarTurmaAluno.php?idUsuario=<?= (int) $aluno['idUsuario'] ?>"class="botao botao-editar">Editar turma</a>
                                <a href="desempenhoAluno.php?idUsuario=<?= (int) $aluno['idUsuario'] ?>"class="botao botao-atividades" >Ver desempenho</a>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="tabela-container">
            <div class="sem-alunos">

                <p>
                    <?php if ($turma): ?>
                        Nenhum aluno está cadastrado nesta turma.
                    <?php else: ?>
                        Nenhum aluno está cadastrado nesta instituição.
                    <?php endif; ?>
                </p>

            </div>
        </div>

    <?php endif; ?>

</main>

</body>
</html>

<?php

mysqli_stmt_close($stmtAlunos);

require_once("../../../assets/templates/footer.php");

?>
