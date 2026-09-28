<?php
require_once __DIR__ . "/../../config/db_connect.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --------------------------------------------------
// VALIDAÇÃO AUTOMÁTICA DO PERFIL PELA PASTA
// --------------------------------------------------

$pastasPermitidas = [
    'aluno',
    'professor',
    'gestor'
];

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
    if (
        !isset($_SESSION['usuarioLogado']) ||
        $_SESSION['usuarioLogado'] != true
    ) {
        header("Location: ../../../login.php");
        exit;
    }

    // Usuário precisa ter o mesmo perfil da pasta
    if ($perfilUsuario !== $perfilPagina) {
        header("Location: ../../../login.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/maria/PROJETO/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@400;700&amp;display=swap" rel="stylesheet">
    <title><?php 
    if(isset($tituloPagina)){
        echo "AprimorAI- $tituloPagina";
    }else{
        echo "AprimorAI";
    }?></title>
</head>

<body>
<main>