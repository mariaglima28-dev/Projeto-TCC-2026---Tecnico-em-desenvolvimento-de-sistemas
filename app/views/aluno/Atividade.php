<?php
session_start();
require_once("../../../assets/templates/header.php");
$conexao = conectar();

// Consulta as atividades diretamente no banco de dados.
$sql = "SELECT
            a.idAtividade,
            a.titulo,
            a.descricao,
            a.prazo,
            a.dataCriacao,
            au.titulo AS aula,
            d.diciplina AS materia
        FROM atividades a
        INNER JOIN aulas au ON au.idAula = a.idAula
        INNER JOIN diciplinas d ON d.idDiciplina = au.idDiciplina
        ORDER BY a.dataCriacao DESC, a.idAtividade DESC";

$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

// Transforma o resultado em array para facilitar a criação dos filtros.
$atividades = [];
if ($resultado) {
    while ($linha = mysqli_fetch_assoc($resultado)) {
        $atividades[] = $linha;
    }
}

$materias = array_values(
    array_unique(
        array_filter(array_column($atividades, "materia"))
    )
);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas atividades</title>

    <style>
    /* Menu lateral */
    .menu {
        position: fixed;
        top: 35px;
        left: 0;
        width: 109px;
        height: calc(100vh - 35px);
        background-color: #ffead9;
        border: 1px solid #168bff;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        padding: 8px 0;
    }

    /* Links do menu */
    .menu a {
        display: block;
        padding: 12px 13px;
        color: #000;
        text-decoration: none;
        font-family: Arial, sans-serif;
        font-size: 15px;
        font-weight: bold;
    }

    /* Efeito ao passar o mouse */
    .menu a:hover {
        background-color: #ffd6b5;
        color: #e6537b;
    }

    /* Conteúdo principal */
    .conteudo {
        margin-left: 109px;
        padding: 15px;
    }

    /* Barra superior */
    .topo {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 35px;
        background-color: #fff;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding-right: 30px;
        box-sizing: border-box;
    }

    .topo a {
        color: #8aaec6;
        text-decoration: none;
        font-family: Arial, sans-serif;
    }
    </style>
</head>

<body>
    <header class="topo">
        <a href="index.php">Home</a>
    </header>
    <nav class="menu">
        <a href="turmas.php">Turmas</a>
        <a href="materias.php">Matérias</a>
        <a href="index.php">Home</a>
    </nav>
    <main class="conteudo">
            
        <h1>Minhas atividades</h1>

        <div class="filtros">
            <label for="filtroNome">Nome da atividade:</label>
            <input type="search" id="filtroNome" placeholder="Digite o nome da atividade">

            <label for="filtroMateria">Matéria:</label>
            <select id="filtroMateria">
                <option value="">Todas</option>
                <?php foreach ($materias as $materia): ?>
                    <option value="<?= htmlspecialchars($materia, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($materia, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="filtroData">Data de criação:</label>
            <input type="date" id="filtroData">

            <button type="button" id="botaoFiltrar">Filtrar</button>
            <button type="button" id="botaoLimpar">Mostrar todas</button>
        </div>

        <?php if (empty($atividades)): ?>
            <p>Nenhuma atividade foi cadastrada ainda.</p>
        <?php else: ?>
            <?php foreach ($atividades as $atividade): ?>
                <article class="atividade"
                    data-titulo="<?= htmlspecialchars($atividade['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    data-materia="<?= htmlspecialchars($atividade['materia'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    data-data="<?= htmlspecialchars($atividade['dataCriacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    <h2>
                        <?= htmlspecialchars($atividade['titulo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </h2>

                    <p>
                        <?= nl2br(htmlspecialchars($atividade['descricao'] ?? '', ENT_QUOTES, 'UTF-8')) ?>
                    </p>

                    <p>
                        <strong>Matéria:</strong>
                        <?= htmlspecialchars($atividade['materia'] ?? 'Não informada', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <p>
                        <strong>Aula:</strong>
                        <?= htmlspecialchars($atividade['aula'] ?? 'Não informada', ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <small>
                        Criada em:
                        <?= htmlspecialchars($atividade['dataCriacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                        · Prazo:
                        <?= htmlspecialchars($atividade['prazo'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </small>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <script src="filtrarAtividade.js"></script>
    </main>
</body>
</html>
