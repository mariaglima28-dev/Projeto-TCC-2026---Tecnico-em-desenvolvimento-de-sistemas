
<?php
session_start();

// Carrega a conexão com o banco
require_once __DIR__ . "/../../../config/db_connect.php";

// Verifica se o gestor está logado
if (
    !isset($_SESSION['tipoUser']) ||
    $_SESSION['tipoUser'] !== 'Gestor' ||
    !isset($_SESSION['idInstituicao'])
) {
    header("Location: ../../../login.php");
    exit;
}

$tituloPagina = "CADASTRAR TURMA";
$conexao = conectar();

$mensagem = "";
$tipoMensagem = "";

// Processa o formulário
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $serie = trim($_POST["serie"] ?? "");
    $idInstituicao = (int) $_SESSION["idInstituicao"];

    if ($serie === "") {
        $mensagem = "Informe a série da turma.";
        $tipoMensagem = "erro";

    } else {

        $sql = "INSERT INTO turmas (serie, idInstituicao)
                VALUES (?, ?)";

        $stmt = mysqli_prepare($conexao, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $serie,
                $idInstituicao
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                header("Location: turmas.php?cadastro=sucesso");
                exit;

            } else {
                $mensagem = "Não foi possível cadastrar a turma.";
                $tipoMensagem = "erro";
                mysqli_stmt_close($stmt);
            }

        } else {
            $mensagem = "Erro ao preparar o cadastro da turma.";
            $tipoMensagem = "erro";
        }
    }
}

// Carrega o cabeçalho depois do processamento e dos redirecionamentos
require_once __DIR__ . "/../../../assets/templates/header.php";
?>

<img
    src="/mariag/PROJETO/assets/img/Qualificação TCC.svg"
    alt="Fundo geométrico"
    class="bg-canvas"
>

<main class="main-workspace">
    <div class="auth-card">

        <div class="card-header">
            <h2>Cadastrar turma</h2>
            <p>Preencha os dados para adicionar uma turma à instituição.</p>
        </div>

        <form method="POST">
            <div class="input-group">
                <label class="label-row" for="serie">
                    Série da turma:
                </label>

                <input
                    type="text"
                    id="serie"
                    name="serie"
                    placeholder="Ex.: 1º ano do Ensino Médio"
                    required
                >
            </div>

            <button type="submit" class="btn-submit">
                Cadastrar turma
            </button>

            <div class="mensagem <?= htmlspecialchars($tipoMensagem) ?>">
                <?= htmlspecialchars($mensagem) ?>
            </div>
        </form>

    </div>
</main>

<?php require_once __DIR__ . "/../../../assets/templates/footer.php"; ?>
