<?php

$titulopagina = "ALUNOS";

require_once("../../../assets/templates/header.php");


$conexao = conectar();

/*
|--------------------------------------------------------------------------
| VERIFICAR INSTITUIÇÃO DO GESTOR
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["idInstituicao"])) {
    echo "<p>Instituição não encontrada.</p>";
    exit;
}

$idInstituicao = $_SESSION["idInstituicao"];


/*
|--------------------------------------------------------------------------
| PEGAR ID DA TURMA
|--------------------------------------------------------------------------
*/

$idTurma = isset($_GET["idTurma"]) ? (int) $_GET["idTurma"] : 0;

if ($idTurma <= 0) {
    echo "<p>Turma não encontrada.</p>";
    exit;
}


/*
|--------------------------------------------------------------------------
| BUSCAR TURMA
|--------------------------------------------------------------------------
*/

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

if (!$turma) {
    echo "<p>Essa turma não pertence à sua instituição.</p>";
    exit;
}


/*
|--------------------------------------------------------------------------
| BUSCAR ALUNOS DA TURMA
|--------------------------------------------------------------------------
*/

$sqlAlunos = "
    SELECT
        idUsuario,
        nome,
        email,
        cpf,
        dataNascimento
    FROM usuarios
    WHERE idTurma = ?
    AND idInstituicao = ?
    AND tipoUser = 'Aluno'
    ORDER BY nome
";

$stmtAlunos = mysqli_prepare($conexao, $sqlAlunos);

mysqli_stmt_bind_param(
    $stmtAlunos,
    "ii",
    $idTurma,
    $idInstituicao
);

mysqli_stmt_execute($stmtAlunos);

$resultadoAlunos = mysqli_stmt_get_result($stmtAlunos);

?>

<main class="pagina-alunos">
<a
    href="turmas.php"
    class="botao-voltar"
>
    ← Voltar para turmas
</a>


<div class="cabecalho-alunos">

    <h1>
        Alunos - <?php echo htmlspecialchars($turma["serie"]); ?>
    </h1>

    <p>
        Alunos cadastrados nesta turma.
    </p>

</div>


<?php if (mysqli_num_rows($resultadoAlunos) > 0): ?>

    <div class="tabela-container">

        <table class="tabela-alunos">

            <thead>

                <tr>

                    <th>Nome</th>

                    <th>E-mail</th>

                    <th>CPF</th>

                    <th>Data de nascimento</th>

                </tr>

            </thead>

            <tbody>

                <?php while ($aluno = mysqli_fetch_assoc($resultadoAlunos)): ?>

                    <tr>

                        <td>
                            <?php
                            echo htmlspecialchars($aluno["nome"]);
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars($aluno["email"]);
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars($aluno["cpf"]);
                            ?>
                        </td>

                        <td>
                            <?php
                            echo htmlspecialchars($aluno["dataNascimento"]);
                            ?>
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
                Nenhum aluno está cadastrado nesta turma.
            </p>

        </div>

    </div>

<?php endif; ?>
```

</main>

</body>

</html>

<?php

mysqli_stmt_close($stmtTurma);
mysqli_stmt_close($stmtAlunos);

require_once("../../../assets/templates/footer.php");

?>
