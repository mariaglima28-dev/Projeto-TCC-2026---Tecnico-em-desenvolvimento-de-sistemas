<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$tempoLimite = 1800; // 30 minutos

if (isset($_SESSION['ultimoAcesso'])) {

    $tempoInativo = time() - $_SESSION['ultimoAcesso'];

    if ($tempoInativo > $tempoLimite) {

        session_unset();
        session_destroy();

        header("Location: /maria/PROJETO/index.php?erro=sessao_expirada");
        exit;
    }
}