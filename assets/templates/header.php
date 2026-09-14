<?php
session_start();
require_once('config/conexao.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$titulopagina?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php if(isset($_SESSION['usuarioLogado'])): ?>
    <header class="header">
        <nav>
            <img src='img/logo-circular.png'class='logo'>
            <div class='botoes'>
                <a href="index.php">Home</a>
                <a href="leitor.php">Leitores</a>
                <a href="livro.php">Livros</a>
                <a href="emprestimo.php">Emprestimos</a>
                <a href="logout.php?logout=true">Sair</a>
            </div>
        </nav>
    </header>
<?php endif; ?>
<main>