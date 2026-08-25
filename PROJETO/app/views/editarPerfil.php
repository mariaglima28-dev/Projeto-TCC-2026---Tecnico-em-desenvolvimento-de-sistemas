<?php
require_once "./../models/Usuario.php";
require_once "./../models/Aluno.php";
require_once "./../models/Professor.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="salvarPerfil.php" method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?php echo $usuario->getNome(); ?>">
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $usuario->getEmail(); ?>">
        <label>CPF:</label>
        <input type="text" name="cpf" value="<?php echo $usuario->getCpf(); ?>">
        <button type="submit">
            Salvar Alterações
        </button>
    </form>
</body>
</html>