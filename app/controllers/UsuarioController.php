<?php

require_once 'db_connect.php';

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST['nome'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $data_nasc = $_POST['data_nasc'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';
    $perfil = $_POST['perfil'] ?? 'Aluno';

    // Verificar se as senhas são iguais
    if ($senha !== $confirma_senha) {

        $mensagem = "As senhas não coincidem.";

    } else {

        // Verificar se o email já existe
        $sql = "SELECT id FROM usuarios WHERE email = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {

            $mensagem = "Este email já está cadastrado.";

        } else {

            // Criptografar senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            // Inserir usuário
            $sql = "INSERT INTO usuarios 
                    (nome, email, senha, cpf, data_nasc, tipo_user)
                    VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ssssss",
                $nome,
                $email,
                $senha_hash,
                $cpf,
                $data_nasc,
                $perfil
            );

            if (mysqli_stmt_execute($stmt)) {

                $mensagem = "Cadastro realizado com sucesso!";

            } else {

                $mensagem = "Erro ao cadastrar: " . mysqli_error($conn);
            }
        }

        mysqli_stmt_close($stmt);
    }
}
?>