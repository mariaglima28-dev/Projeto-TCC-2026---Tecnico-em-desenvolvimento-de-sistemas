<?php

session_start();

$nome = $_POST['nome'];
$email = $_POST['email'];
$cpf = $_POST['cpf'];

// Atualiza os dados na sessão
$_SESSION['nome'] = $nome;
$_SESSION['email'] = $email;
$_SESSION['cpf'] = $cpf;

echo "Perfil atualizado com sucesso!";

/* Em Json
<?php

$usuario = [
    "nome" => $_POST['nome'],
    "email" => $_POST['email'],
    "cpf" => $_POST['cpf']
];

file_put_contents(
    "../data/usuario.json",
    json_encode($usuario, JSON_PRETTY_PRINT)
);

header("Location: perfil.php");
*/
