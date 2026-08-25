<?php
function conectar(){
    $host = "localhost:3306";
    $bd = "biblios_b";
    $username = "root";
    $password = "";

    try{
        $conexao = mysqli_connect($host, $username, $password, $bd);
        if(mysqli_connect_errno()){
            throw new Exception("Erro ao conectar ao banco de dados: " . mysqli_connect_errno());
        }
        mysqli_set_charset($conexao, "utf8mb4");
        return $conexao;
    }
    catch(Exception $e){
        return "Erro ao conectar ao banco de dados: " . $e->getMessage();
    }
}
?>