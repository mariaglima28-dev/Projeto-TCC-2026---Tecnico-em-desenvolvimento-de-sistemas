
<?php
session_start();
$tituloPagina = "EDITAR PERFIL";
require_once "../../../assets/templates/header.php";


if (empty($_SESSION["idUsuario"])) {
    header("Location: login.php");
    exit;
}

$conexao = conectar();
$idUsuario = (int) $_SESSION["idUsuario"];
$mensagemErro = "";

// Salvar alterações
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $cpf = trim($_POST["cpf"] ?? "");

    if (
        $nome === "" ||
        $email === "" ||
        $cpf === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        $mensagemErro = "Preencha todos os campos corretamente.";
    } else {
        try {
            $sql = "UPDATE usuarios
                    SET nome = ?, email = ?, cpf = ?
                    WHERE idUsuario = ?";

            $stmt = $conexao->prepare($sql);
            $stmt->bind_param(
                "sssi",
                $nome,
                $email,
                $cpf,
                $idUsuario
            );

            $stmt->execute();

            $_SESSION["nome"] = $nome;
            $_SESSION["email"] = $email;

            header("Location: Perfil.php?atualizado=1");
            exit;

        } catch (mysqli_sql_exception $e) {
            error_log("Erro ao editar perfil: " . $e->getMessage());

            if ((int) $e->getCode() === 1062) {
                $mensagemErro = "Esse email ou CPF já está cadastrado.";
            } else {
                $mensagemErro =
                    "Não foi possível salvar as alterações.";
            }
        }
    }
}

// Buscar dados atuais do usuário
$sql = "SELECT nome, email, cpf, tipoUser, foto
        FROM usuarios
        WHERE idUsuario = ?";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<main class="perfil-page">

    <h1 class="perfil-titulo">Editar perfil</h1>
    <div class="perfil-layout">

        <!-- Informações do usuário -->
        <aside class="perfil-sidebar">

            
<div class="perfil-foto">

    <?php if (!empty($usuario["foto"])): ?>

        <?php
        $fotoBase64 = base64_encode($usuario["foto"]);

        // Detecta o formato real da imagem binária
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $tipoImagem = $finfo->buffer($usuario["foto"]);

        $tiposPermitidos = [
            "image/jpeg",
            "image/png",
            "image/webp",
            "image/gif"
        ];

        if (!in_array($tipoImagem, $tiposPermitidos, true)) {
            $tipoImagem = "image/jpeg";
        }

        $fotoSrc = "data:" . $tipoImagem . ";base64," . $fotoBase64;
        ?>

        <img src="<?= htmlspecialchars($fotoSrc, ENT_QUOTES, "UTF-8") ?>" id="previewFoto" alt="Foto de perfil">

    <?php else: ?>

        <img src="../../assets/img/perfil-padrao.jpg" id="previewFoto" alt="Foto de perfil padrão">

    <?php endif; ?>

     <label
                    for="foto"
                    class="botao-camera"
                    title="Alterar foto"
                >
                    📷
                </label>

                <input
                    type="file"
                    id="foto"
                    name="foto"
                    accept="image/jpeg,image/png,image/webp"
                    hidden
                >

</div>


            <p>
                <?= htmlspecialchars(
                    $usuario["tipoUser"],
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </p>

        </aside>

        <!-- Formulário de edição -->
        <section class="auth-card">

            <h2>Dados pessoais</h2>

            <form method="POST">

                <div class="input-group">
                    <label class="label-row" for="nome">Nome:</label>

                    <input
                        id="nome"
                        type="text"
                        name="nome"
                        maxlength="255"
                        value="<?= htmlspecialchars(
                            $usuario["nome"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>"
                        required
                    >
                
                    <label class="label-row" for="email">Email:</label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        maxlength="255"
                        value="<?= htmlspecialchars(
                            $usuario["email"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>"
                        required
                    >
                

                    <label class="label-row" for="cpf">CPF:</label>

                    <input
                        id="cpf"
                        type="text"
                        name="cpf"
                        maxlength="14"
                        value="<?= htmlspecialchars(
                            $usuario["cpf"],
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>"
                        required
                    >
                </div>

                <nav class="perfil-acoes"
                     aria-label="Ações do formulário">

                    <a
                        href="Perfil.php"
                        class="botao-cancelar"
                    >
                        Cancelar
                    </a>

                    <button
                        type="submit"
                        class="botao-salvar"
                    >
                        Salvar alterações
                    </button>

                </nav>

            </form>

        </section>

    </div>

</main>

<?php require_once "../../assets/templates/footer.php"; ?>