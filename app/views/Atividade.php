<?php
require_once "../../Atividade.php";
require_once "../../Aula.php";

$atividades = new Atividade($db);
$lista = $atividades->listar();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atividades</title>
</head>
<body>
    <div class="menu">
        <a href="turmas.php"><button type="submit">Turmas</button>
        <a href="materias.php"><button type="submit">Matérias</button>
        <a href="index.php"><button type="submit">Home</button>
    </div>

    <div class="todasAsTarefas">
        <h1>Minhas Atividades</h1>
        <?php foreach ($lista as $atividade): ?>

            <h2><?= $atividade['titulo'] ?></h2>
            <p><?= $atividade['descricao'] ?></p>
            <p><?= $atividade['data_postagem'] ?></p>

        <?php endforeach; ?>
    </div>
    ---------------------------------------------------------------------------------

    <h4>Filtrar Atividades<h4>
    <a href="filtrarAtividade.php"><button type="submit">Filtrar</button></a>
    
    ---------------------------------------------------------------------------------
    
    <h2 id="QuizNivelamento">Fazer o Quiz</h2>
    <a href="#QuizNivelamento"><button type="submit">Fazer o Quiz</button></a>

    ---------------------------------------------------------------------------------

    <h2>Atividades Postadas</h2>

    <?php foreach ($lista as $atividade): ?>

        <div class="atividade">
            <h2>
                <?= htmlspecialchars($atividade['titulo']) ?>
            </h2>
            <p>
                <?= nl2br(htmlspecialchars($atividade['descricao'])) ?>
            </p>
            <small>
                Postado em:
                <?= htmlspecialchars($atividade['data_postagem']) ?>
            </small>

        </div>

    <?php endforeach; ?>
</body>
</html>
