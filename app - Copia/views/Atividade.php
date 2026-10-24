<?php
session_start();
require_once __DIR__ . "/conexao.php";

// Consulta diretamente o banco, sem usar classe/modelo.
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

$stmt = $db->query($sql);
$lista = $stmt->fetchAll();

$materias = array_values(
    array_unique(
        array_filter(array_column($lista, "materia"))
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
        body {
            font-family: Arial, sans-serif;
            max-width: 950px;
            margin: 30px auto;
            padding: 0 16px;
        }

        .menu {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
        }

        .atividade {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 14px;
            margin: 12px 0;
        }

        label {
            margin-right: 6px;
        }

        .filtros {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        button {
            padding: 8px 12px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <nav class="menu">
        <a href="turmas.php">Turmas</a>
        <a href="materias.php">Matérias</a>
        <a href="index.php">Home</a>
    </nav>

    <h1>Minhas atividades</h1>

    <div class="filtros">
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

    <?php if (!$lista): ?>
        <p>Nenhuma atividade foi cadastrada ainda.</p>
    <?php endif; ?>

    <?php foreach ($lista as $atividade): ?>
        <article class="atividade"
            data-materia="<?= htmlspecialchars($atividade['materia'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            data-data="<?= htmlspecialchars($atividade['dataCriacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <h2>
                <?= htmlspecialchars($atividade['titulo'], ENT_QUOTES, 'UTF-8') ?>
            </h2>

            <p>
                <?= nl2br(htmlspecialchars($atividade['descricao'], ENT_QUOTES, 'UTF-8')) ?>
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
                <?= htmlspecialchars($atividade['dataCriacao'], ENT_QUOTES, 'UTF-8') ?>
                · Prazo:
                <?= htmlspecialchars($atividade['prazo'], ENT_QUOTES, 'UTF-8') ?>
            </small>
        </article>
    <?php endforeach; ?>

    <script src="filtrarAtividade.js"></script>
</body>
</html>