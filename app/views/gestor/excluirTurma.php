<?php
session_start();
require_once __DIR__ . "/../../../assets/templates/header.php";

$conexao = conectar();

$idInstituicao = (int) $_SESSION['idInstituicao'];

// Recebe o ID enviado pelo botão
$idTurma = filter_input(INPUT_GET, 'idTurma', FILTER_VALIDATE_INT);

// Se o ID for inválido, volta para a listagem
if (!$idTurma || $idTurma <= 0) {
    header("Location: turmas.php?erro=" . urlencode(
        "Identificação da turma inválida."
    ));
    exit;
}

// Confirma que a turma pertence à instituição do gestor
$sqlTurma = "
    SELECT idTurma
    FROM turmas
    WHERE idTurma = ?
      AND idInstituicao = ?
";

$stmtTurma = mysqli_prepare($conexao, $sqlTurma);

if (!$stmtTurma) {
    header("Location: turmas.php?erro=" . urlencode(
        "Não foi possível verificar a turma."
    ));
    exit;
}

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
    header("Location: turmas.php?erro=" . urlencode(
        "Turma não encontrada ou sem permissão para excluí-la."
    ));
    exit;
}

// Verifica se existem alunos vinculados
$sqlAlunos = "
    SELECT COUNT(*) AS total
    FROM usuarios
    WHERE idTurma = ?
      AND idInstituicao = ?
      AND tipoUser = 'Aluno'
";

$stmtAlunos = mysqli_prepare($conexao, $sqlAlunos);

if (!$stmtAlunos) {
    header("Location: turmas.php?erro=" . urlencode(
        "Não foi possível verificar os alunos da turma."
    ));
    exit;
}

mysqli_stmt_bind_param(
    $stmtAlunos,
    "ii",
    $idTurma,
    $idInstituicao
);

mysqli_stmt_execute($stmtAlunos);

$resultadoAlunos = mysqli_stmt_get_result($stmtAlunos);
$dadosAlunos = mysqli_fetch_assoc($resultadoAlunos);

mysqli_stmt_close($stmtAlunos);

// Não permite excluir turmas com alunos
if ((int) $dadosAlunos['total'] > 0) {
    header("Location: turmas.php?erro=" . urlencode(
        "Esta turma não pode ser excluída porque possui alunos vinculados."
    ));
    exit;
}

// Exclui a turma vazia
$sqlExcluir = "
    DELETE FROM turmas
    WHERE idTurma = ?
      AND idInstituicao = ?
";

$stmtExcluir = mysqli_prepare($conexao, $sqlExcluir);

if (!$stmtExcluir) {
    header("Location: turmas.php?erro=" . urlencode(
        "Não foi possível preparar a exclusão da turma."
    ));
    exit;
}

mysqli_stmt_bind_param(
    $stmtExcluir,
    "ii",
    $idTurma,
    $idInstituicao
);

if (!mysqli_stmt_execute($stmtExcluir)) {
    mysqli_stmt_close($stmtExcluir);

    header("Location: turmas.php?erro=" . urlencode(
        "O banco de dados não permitiu excluir a turma."
    ));
    exit;
}

$excluida = mysqli_stmt_affected_rows($stmtExcluir) === 1;

mysqli_stmt_close($stmtExcluir);

if ($excluida) {
    header("Location: turmas.php?exclusao=sucesso");
    exit;
}

header("Location: turmas.php?erro=" . urlencode(
    "A turma não foi excluída."
));
exit;