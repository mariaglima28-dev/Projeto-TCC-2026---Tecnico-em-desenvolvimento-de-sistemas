<?php
require_once "../../Aluno.php";
require_once "../../Medalha.php";

$atividades = []; $quizzes = [];
$medalhas = [ 
    new Medalha( 
        "Quiz Master", 
        "Complete 10 quizzes", 
        10, 8 ), 

    new Medalha(
         "Aluno Dedicado", 
         "Complete 20 atividades", 
         20, 12 ),
    
    new Medalha( 
        "Participativo", 
        "Participe de 5 interações", 
        5, 5 )
];

$aluno = new Aluno(
    "Danielle",
    "email@email.com",
    "123456",
    "000.000.000-00",
    "01/01/2000",
    "Aluno",
    "SENAI",
    "Turma A",
    "Aluno",
    $atividades,
    $quizzes,
    $medalhas
);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Conquistas</title>
</head>
<body>
    <h1>Minhas Conquistas</h1>

    <div class="dashboard">
        <div class="card">
            <h3>Atividades Pendentes</h3>
            <p><?= $aluno->contarAtividadesPendentes() ?></p>
        </div>

        <div class="card">
            <h3>Quizzes Realizados</h3>
            <p><?= $aluno->contarQuizzes() ?></p>
        </div>

        <div class="card">
            <h3>Medalhas Conquistadas</h3>
            <p><?= $aluno->contarMedalhas() ?></p>
        </div>
    </div>

    <h2>Minhas Medalhas</h2>

    <?php foreach ($medalhas as $medalha): ?>
        <div class="medalha">
            <h3>
                <?php if ($medalha->estaConquistada()): ?>
                    🏆
                <?php else: ?>
                    🔒
                <?php endif; ?>
                <?= $medalha->getNome() ?>
            </h3>

            <p><?= $medalha->getDescricao() ?></p>

            <?php 
                $porcentagem =
                $medalha->calcularProgresso();
            ?>

            <div class="progresso">
                <div
                    class="barra"
                    style="width: <?= $porcentagem ?>%;">
                    <?= round($porcentagem) ?>%
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>