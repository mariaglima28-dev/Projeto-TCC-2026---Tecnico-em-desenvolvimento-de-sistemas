<?php
session_start();
require_once __DIR__ . "/conexao.php";

if (empty($_SESSION["idUsuario"])) {
    header("Location: login.php");
    exit;
}

$idAluno = (int) $_SESSION["idUsuario"];

// Confere se o usuário é aluno.
$stmt = $db->prepare(
    "SELECT tipoUser FROM usuarios WHERE idUsuario = :id"
);

$stmt->execute(["id" => $idAluno]);
$tipoUser = $stmt->fetchColumn();

if ($tipoUser !== "Aluno") {
    http_response_code(403);
    exit("Esta página está disponível somente para alunos.");
}

// Conta as atividades que ainda não possuem resposta do aluno.
$sqlAtividades = "SELECT COUNT(DISTINCT a.idAtividade)
                  FROM atividades a
                  LEFT JOIN respostas r
                    ON r.idAtividade = a.idAtividade
                    AND r.idAluno = :id
                  WHERE r.idResposta IS NULL";

$stmt = $db->prepare($sqlAtividades);
$stmt->execute(["id" => $idAluno]);
$atividadesPendentes = (int) $stmt->fetchColumn();

// Conta as atividades respondidas pelo aluno.
$sqlQuizzes = "SELECT COUNT(DISTINCT r.idAtividade)
               FROM respostas r
               WHERE r.idAluno = :id";

$stmt = $db->prepare($sqlQuizzes);
$stmt->execute(["id" => $idAluno]);
$atividadesRespondidas = (int) $stmt->fetchColumn();

// Busca as medalhas e verifica quais foram conquistadas.
$sqlConquistas = "SELECT
                      m.idConquista,
                      m.titulo,
                      m.descricao,
                      m.criterios,
                      CASE
                          WHEN c.idConquistaAluno IS NULL THEN 0
                          ELSE 1
                      END AS conquistada,
                      c.dataConquista
                  FROM metas m
                  LEFT JOIN conquistas c
                    ON c.idConquista = m.idConquista
                    AND c.idAluno = :id
                  ORDER BY m.idConquista";

$stmt = $db->prepare($sqlConquistas);
$stmt->execute(["id" => $idAluno]);
$medalhas = $stmt->fetchAll();

$conquistasObtidas = 0;

foreach ($medalhas as $medalha) {
    if ((int) $medalha["conquistada"] === 1) {
        $conquistasObtidas++;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas conquistas</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 30px auto;
            padding: 0 16px;
        }

        .dashboard {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .card,
        .medalha {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            margin: 12px 0;
        }

        .card {
            min-width: 180px;
        }

        .progresso {
            background: #eee;
            border-radius: 10px;
            overflow: hidden;
            height: 24px;
        }

        .barra {
            height: 24px;
            background: #39845a;
            color: white;
            text-align: center;
            line-height: 24px;
            max-width: 100%;
        }
    </style>
</head>

<body>
    <h1>Minhas conquistas</h1>

    <div class="dashboard">
        <section class="card">
            <h3>Atividades pendentes</h3>
            <p><?= $atividadesPendentes ?></p>
        </section>

        <section class="card">
            <h3>Atividades respondidas</h3>
            <p><?= $atividadesRespondidas ?></p>
        </section>

        <section class="card">
            <h3>Medalhas conquistadas</h3>
            <p><?= $conquistasObtidas ?></p>
        </section>
    </div>

    <h2>Minhas medalhas</h2>

    <?php if (!$medalhas): ?>
        <p>Nenhuma medalha foi cadastrada na tabela metas.</p>
    <?php endif; ?>

    <?php foreach ($medalhas as $medalha): ?>
        <?php
        // A tabela metas não possui uma meta numérica de progresso.
        // Por isso, mostra 100% se conquistada e 0% caso contrário.
        $porcentagem =
            ((int) $medalha["conquistada"] === 1) ? 100 : 0;
        ?>

        <section class="medalha">
            <h3>
                <?= $porcentagem === 100 ? "🏆" : "🔒" ?>
                <?= htmlspecialchars(
                    $medalha["titulo"],
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </h3>

            <p>
                <?= htmlspecialchars(
                    $medalha["descricao"],
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </p>

            <p>
                <strong>Critérios:</strong>
                <?= htmlspecialchars(
                    $medalha["criterios"],
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </p>

            <div class="progresso">
                <div
                    class="barra"
                    style="width:<?= $porcentagem ?>%"
                >
                    <?= $porcentagem ?>%
                </div>
            </div>

            <?php if (!empty($medalha["dataConquista"])): ?>
                <small>
                    Conquistada em
                    <?= htmlspecialchars(
                        $medalha["dataConquista"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </small>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</body>
</html>