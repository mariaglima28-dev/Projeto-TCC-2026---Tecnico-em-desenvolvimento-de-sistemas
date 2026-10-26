
<?php
$titulopagina = "EDITAR TURMA";

require_once("../../../assets/templates/header.php");

$conexao = conectar();

// Verifica se o ID da turma foi informado
if (!isset($_GET["idTurma"]) || !is_numeric($_GET["idTurma"])) {
    echo "<p>Turma inválida.</p>";
    require_once("../../../assets/templates/footer.php");
    exit;
}

$idTurma = (int) $_GET["idTurma"];

// Obtém a instituição do gestor logado
if (!isset($_SESSION["idInstituicao"])) {
    echo "<p>Instituição não encontrada.</p>";
    require_once("../../../assets/templates/footer.php");
    exit;
}

$idInstituicao = (int) $_SESSION["idInstituicao"];

$mensagem = "";
$tipoMensagem = "";

// Busca a turma pertencente à instituição do gestor
$sql = "SELECT idTurma, serie
        FROM turmas
        WHERE idTurma = ?
        AND idInstituicao = ?";

$stmt = mysqli_prepare($conexao, $sql);
mysqli_stmt_bind_param($stmt, "ii", $idTurma, $idInstituicao);
mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);
$turma = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($stmt);

if (!$turma) {
    echo "<p>Turma não encontrada.</p>";
    require_once("../../../assets/templates/footer.php");
    exit;
}

// Salva a alteração
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $serie = trim($_POST["serie"] ?? "");

    // Remove espaços extras
    $serie = preg_replace('/\s+/u', ' ', $serie);

    // Padroniza o símbolo ordinal
    $serie = str_replace('°', 'º', $serie);

    // Converte todas as letras para maiúsculas
    $serie = mb_strtoupper($serie, 'UTF-8');

    if ($serie === "") {
        $mensagem = "Informe a série da turma.";
        $tipoMensagem = "erro";
    } elseif (mb_strlen($serie) > 50) {
        $mensagem = "A série deve ter no máximo 50 caracteres.";
        $tipoMensagem = "erro";
    } else {

        // Verifica se já existe outra turma com a mesma série
        $sql = "SELECT idTurma
                FROM turmas
                WHERE serie = ?
                AND idInstituicao = ?
                AND idTurma != ?";

        $stmt = mysqli_prepare($conexao, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sii",
            $serie,
            $idInstituicao,
            $idTurma
        );

        mysqli_stmt_execute($stmt);

        $resultadoDuplicado = mysqli_stmt_get_result($stmt);
        $turmaDuplicada = mysqli_fetch_assoc($resultadoDuplicado);

        mysqli_stmt_close($stmt);

        if ($turmaDuplicada) {

            $mensagem = "Já existe uma turma com essa série na sua instituição.";
            $tipoMensagem = "erro";

        } else {

            // Atualiza a série da turma
            $sql = "UPDATE turmas
                    SET serie = ?
                    WHERE idTurma = ?
                    AND idInstituicao = ?";

            $stmt = mysqli_prepare($conexao, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "sii",
                $serie,
                $idTurma,
                $idInstituicao
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header("Location: turmas.php?sucesso=edicao");
                exit;

            } else {

                $mensagem = "Não foi possível atualizar a turma.";
                $tipoMensagem = "erro";

                mysqli_stmt_close($stmt);
            }
        }
    }
    // Mantém o valor digitado caso haja erro
    $turma["serie"] = $serie;
}
?>

<main class="container-editar-turma">
    <h1>Editar turma</h1>

    <p>Altere a série da turma abaixo.</p>

    <?php if ($mensagem !== ""): ?>
        <p class="mensagem <?= htmlspecialchars($tipoMensagem) ?>">
            <?= htmlspecialchars($mensagem) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <div class="campo">
            <label for="serie">Série da turma</label>

            <input
                type="text"
                id="serie"
                name="serie"
                maxlength="50"
                value="<?= htmlspecialchars($turma["serie"]) ?>"
                required
            >
        </div>

        <div class="botoes">
            <button type="submit">Salvar alterações</button>

            <a href="turmas.php">Cancelar</a>
        </div>
    </form>
</main>

<?php
require_once("../../../assets/templates/footer.php");
?>