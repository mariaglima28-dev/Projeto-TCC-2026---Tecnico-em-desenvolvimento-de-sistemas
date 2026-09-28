<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_escolar');

function conectar()
{
    $conexao = mysqli_connect(
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME
    );

    if (!$conexao) {
        die("Falha na conexão com o MySQL: " . mysqli_connect_error());
    }

    mysqli_set_charset($conexao, "utf8mb4");

    return $conexao;
}