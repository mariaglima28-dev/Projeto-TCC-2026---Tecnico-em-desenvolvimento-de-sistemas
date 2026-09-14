<?php

// Dados do banco
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistema_escolar');


// ==============================
// 1. CONECTAR AO MYSQL
// ==============================

$conectar = mysqli_connect(
    DB_HOST,
    DB_USER,
    DB_PASS
);

if (!$conectar) {

    die(
        "Falha na conexão com o MySQL: "
        . mysqli_connect_error()
    );

}


// ==============================
// 2. CRIAR BANCO SE NÃO EXISTIR
// ==============================

$sql_create_db = "
    CREATE DATABASE IF NOT EXISTS sistema_escolar
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci
";

if (!mysqli_query($conectar, $sql_create_db)) {

    die(
        "Erro ao criar banco de dados: "
        . mysqli_error($conectar)
    );

}


// ==============================
// 3. SELECIONAR BANCO
// ==============================

if (!mysqli_select_db(
    $conectar,
    DB_NAME
)) {

    die(
        "Erro ao selecionar banco de dados: "
        . mysqli_error($conectar)
    );

}


// ==============================
// 4. UTF-8
// ==============================

mysqli_set_charset(
    $conectar,
    "utf8mb4"
);


// ==============================
// 5. CRIAR TABELA USUARIOS
// ==============================

$sql_create_table = "

CREATE TABLE IF NOT EXISTS usuarios (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(255) NOT NULL,

    email VARCHAR(255) NOT NULL UNIQUE,

    senha VARCHAR(255) NOT NULL,

    cpf VARCHAR(14) NOT NULL UNIQUE,

    data_nasc DATE NOT NULL,

    tipo_usuario VARCHAR(50) NOT NULL DEFAULT 'aluno',

    instituicao VARCHAR(255)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4;

";


if (!mysqli_query(
    $conectar,
    $sql_create_table
)) {

    die(
        "Erro ao criar tabela: "
        . mysqli_error($conectar)
    );

}

?>