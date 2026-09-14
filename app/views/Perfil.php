<?php
require_once "./../models/Usuario.php";
require_once "./../models/Aluno.php";
require_once "./../models/Professor.php";
require_once "./../models/Perfil.php";

switch($tipoUser){
    case 'Aluno':
        $nome = $this->nome;
        $email = $this->email;
        $cpf = $this->cpf;
        $instituicao = $this->instituicao;
        $turma = $this->turma;
        break;
    case 'Professor':
        $nome = $this->nome;
        $email = $this->email;
        $cpf = $this->cpf;
        $instituicao = $this->instituicao;
        $disciplina = $this->disciplina;
        break;
    case 'Gestor':
        $nome = $this->nome;
        $email = $this->email;
        $cpf = $this->cpf;
        $instituicao = $this->instituicao;
        break;
    default:
        http_response_code(400);
        echo "<h1>Tipo de Usuário não detectado</h1>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
</head>
<body>
    <div class='topo'>
        <h1>Minhas informações</h1>
        <p><strong>Nome Completo: </strong> <?php echo $nome ?></p>
        <img src="<?php echo $foto; ?>" width="150">
        <form action="uploadFoto.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="foto">
            <button type="submit">Alterar Foto</button>
        </form>
    </div> 
    <div class='mostrarInfo'>
        <p><strong>Email: </strong> <?php echo $email ?></p>
        <p><strong>CPF: </strong> <?php echo $cpf ?></p>
        <p><strong>Instituição: </strong> <?php echo $instituicao ?></p>
        <?php
            if($tipoUser == "Professor"){
                echo "<p><strong>Matéria: </strong>" . $disciplina . "</p>"; 
            }
            if($tipoUser == "Aluno"){
                echo "<p><strong>Turma: </strong>" . $turma . "</p>"; 
            }
        ?>
    </div>
    <div class='MinhasConquistas'>
        <h2>Minhas Conquistas </h2>
        <ul>
        <?php
        foreach($perfil->getConquistas() as $conquista){
            echo "<li>$conquista</li>";
        }
        ?>
        </ul>
    </div>
    <a href="editarPerfil.php">
        <button>Editar Perfil </button>
    </a>

    <a href="logoutPerfil.php">
        <button>Sair</button>
    </a>
</body>
</html>
