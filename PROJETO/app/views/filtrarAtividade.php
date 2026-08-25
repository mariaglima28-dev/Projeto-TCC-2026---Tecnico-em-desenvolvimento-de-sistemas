<?php
require_once "../../Atividade.php";
require_once "../../Aula.php";

$aula = new Aula(1, "POO", new Turma(1, "DS", $professor));

$atividades = [
    new Atividade(1, "Lista de Exercicios", "2026-06-20", $aula),
    new Atividade(2, "Trabalho Final", "2026-06-22", $aula),
    new Atividade(3, "Prova", "2026-06-25", $aula),
];

$nome = $_GET['nome'] ?? "";
$data = $_GET['data'] ?? "";

$resultado = [];

foreach ($atividades as $atividade) {

    $filtrarNome = empty($nome) ||
        $atividade->getEnunciado() == $nome;

    $filtrarData = empty($data) ||
        $atividade->getData() == $data;

    if ($filtrarNome && $filtrarData) {
        $resultado[] = $atividade;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FiltroAtv</title>
</head>
<body>
    <div class="filtroAtv">
        <h2>Pesquisar Atividades</h2>
        <form method="GET">
            <label>Nome da atividade</label><br>
            <input type="text" name="nome" value="<?= $nome ?>">
            <br><br>
            <label>Data</label><br>
            <input type="date" name="data" value="<?= $data ?>">
            <br><br>
            <button type="submit">Pesquisar</button>
        </form>
        <a href="Atividade.php">Limpar</a>

        <h3>Resultado</h3>

        <table border="1" cellpadding="8">

            <tr>
                <th>Atividade</th>
                <th>Data</th>
            </tr>

            <?php

            if(count($resultado) > 0){
                foreach($resultado as $atividade){
                    echo "<tr>";
                    echo "<td>".$atividade->getEnunciado()."</td>";
                    echo "<td>".$atividade->getData()."</td>";
                    echo "</tr>";
                }
            }else{
                echo "<tr>";
                echo "<td colspan='2'>Nenhuma atividade encontrada.</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>