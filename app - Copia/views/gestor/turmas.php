<?php

$titulopagina = "TURMAS";
session_start();
require_once ("../../../assets/templates/header.php");

$conexao = conectar();

/*
|--------------------------------------------------------------------------
| VERIFICAÇÃO DO GESTOR
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["idInstituicao"])) {

    echo "<p>Instituição não encontrada.</p>";
    exit;
}

$idInstituicao = $_SESSION["idInstituicao"];

/*
|--------------------------------------------------------------------------
| BUSCAR TURMAS DA INSTITUIÇÃO
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT 
        t.idTurma,
        t.serie,
        COUNT(u.idUsuario) AS quantidadeAlunos
    FROM turmas t

    LEFT JOIN usuarios u 
        ON u.idTurma = t.idTurma
        AND u.tipoUser = 'Aluno'

    WHERE t.idInstituicao = ?

    GROUP BY t.idTurma, t.serie

    ORDER BY t.serie
";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $idInstituicao
);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>

<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AprimorAI - Turmas</title>

    <style>

        .pagina-turmas {
            padding: 30px;
        }

        .titulo-turmas {
            margin-bottom: 25px;
        }

        .titulo-turmas h1 {
            margin-bottom: 8px;
        }

        .titulo-turmas p {
            color: #666;
        }

        .lista-turmas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .card-turma {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.10);
        }

        .card-turma h2 {
            margin-bottom: 15px;
        }

        .quantidade-alunos {
            margin-bottom: 20px;
            color: #555;
        }

        .botao-alunos {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            background: #4b6cb7;
            color: white;
        }

        .botao-alunos:hover {
            opacity: 0.85;
        }

        .sem-turmas {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            color: #666;
        }

    </style>

</head>

<body>

    <main class="pagina-turmas">

        <div class="titulo-turmas">

            <h1>Minhas Turmas</h1>

            <p>
                Visualize as turmas cadastradas na sua instituição.
            </p>

        </div>

        <?php if (mysqli_num_rows($resultado) > 0): ?>

            <div class="lista-turmas">

                <?php while ($turma = mysqli_fetch_assoc($resultado)): ?>

                    <div class="card-turma">

                        <h2>
                            <?php echo htmlspecialchars($turma["serie"]); ?>
                        </h2>

                        <p class="quantidade-alunos">

                            👥

                            <?php echo $turma["quantidadeAlunos"]; ?>

                            aluno(s)

                        </p>

                        <a
                            class="botao-alunos"
                            href="alunos.php?idTurma=<?php echo $turma["idTurma"]; ?>"
                        >
                            Ver alunos
                        </a>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <div class="sem-turmas">

                <p>
                    Nenhuma turma cadastrada nesta instituição.
                </p>

            </div>

        <?php endif; ?>

    </main>

</body>

</html>

<?php

mysqli_stmt_close($stmt);

require_once("../../../assets/templates/footer.php");

?>