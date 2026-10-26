<?php
require_once __DIR__ . "/../../config/db_connect.php";
require_once __DIR__ . "/verificarSession.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --------------------------------------------------
// VALIDAÇÃO AUTOMÁTICA DO PERFIL PELA PASTA
// --------------------------------------------------

$pastasPermitidas = ['aluno','professor','gestor'];

// Descobre a pasta da página atual
$perfilPagina = strtolower(
    basename(dirname($_SERVER['SCRIPT_FILENAME']))
);

// Perfil do usuário
$perfilUsuario = strtolower(
    trim($_SESSION['tipoUser'] ?? '')
);

if (in_array($perfilPagina, $pastasPermitidas)) {

    // Usuário precisa estar logado
    if (!isset($_SESSION['usuarioLogado']) ||$_SESSION['usuarioLogado'] != true) {
        header("Location: ../../../login.php");
        exit;
    }

    // Usuário precisa ter o mesmo perfil da pasta
    if ($perfilUsuario !== $perfilPagina) {
        header("Location: ../../../login.php");
        exit;
    }
}
$baseUrl = '/mariag/PROJETO';

$tipoUser = strtolower(
    trim($_SESSION['tipoUser'] ?? '')
);

$usuarioLogado = (
    isset($_SESSION['usuarioLogado']) &&
    $_SESSION['usuarioLogado'] == true
);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/mariag/PROJETO/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <title><?php if(isset($tituloPagina)){echo "AprimorAI - $tituloPagina";}else{echo "AprimorAI";}?></title>
</head>
<body>


<?php if ($usuarioLogado): ?>

    <nav class="navbar">

        <?php if ($tipoUser === 'gestor'): ?>

            <a href="<?= $baseUrl ?>/app/views/gestor/index.php">Início</a>
            <a href="<?= $baseUrl ?>/app/views/gestor/alunos.php">Alunos</a>
            <a href="<?= $baseUrl ?>/app/views/gestor/cadastro.php">Cadastro</a>
            <a href="<?= $baseUrl ?>/app/views/gestor/professores.php">Professores</a>
            <a href="<?= $baseUrl ?>/app/views/gestor/turmas.php">Turmas</a>
            <a href="<?= $baseUrl ?>/app/views/gestor/analise.php">Análise de desempenho</a>

        <?php elseif ($tipoUser === 'professor'): ?>

            <a href="<?= $baseUrl ?>/app/views/professor/index.php">Início</a>
            <a href="<?= $baseUrl ?>/app/views/professor/turmas.php">Minhas turmas</a>
            <a href="<?= $baseUrl ?>/app/views/professor/aulas.php">Aulas</a>
            <a href="<?= $baseUrl ?>/app/views/professor/atividades.php">Atividades</a>
            <a href="<?= $baseUrl ?>/app/views/professor/chat.html">Chat</a>

        <?php elseif ($tipoUser === 'aluno'): ?>

            <a href="<?= $baseUrl ?>/app/views/aluno/index.php">Início</a>
            <a href="<?= $baseUrl ?>/app/views/aluno/turmas.php">Minha turma</a>
            <a href="<?= $baseUrl ?>/app/views/aluno/atividades.php">Atividades</a>

        <?php endif; ?>

        <a href="<?= $baseUrl ?>/app/views/perfil/perfil.php">Meu perfil</a>
        <a href="<?= $baseUrl ?>/app/views/perfil/logout.php">Sair</a>

    </nav>

<?php endif; ?>
<main>