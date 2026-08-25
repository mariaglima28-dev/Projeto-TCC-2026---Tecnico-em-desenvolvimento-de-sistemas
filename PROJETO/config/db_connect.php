<?php

require_once '.env';

// 1. Conectar ao MySQL
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

if (!$conn) {
    die("Falha na conexão com o MySQL: " . mysqli_connect_error());
}

// 2. Criar o banco de dados caso não exista
$sql_create_db = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`";

if (!mysqli_query($conn, $sql_create_db)) {
    die("Erro ao criar banco de dados: " . mysqli_error($conn));
}

// 3. Selecionar o banco
if (!mysqli_select_db($conn, DB_NAME)) {
    die("Erro ao selecionar banco de dados: " . mysqli_error($conn));
}

// 4. Definir UTF-8
mysqli_set_charset($conn, "utf8mb4");

// 5. Criar tabela de usuários
$sql_create_table = "CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL UNIQUE,
    data_nasc DATE NOT NULL,
    tipo_user VARCHAR(50) NOT NULL DEFAULT 'Aluno'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (!mysqli_query($conn, $sql_create_table)) {
    die("Erro ao criar tabela: " . mysqli_error($conn));
}