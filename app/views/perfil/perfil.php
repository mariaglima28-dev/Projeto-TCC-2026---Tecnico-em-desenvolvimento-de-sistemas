
<?php
session_start();
$tituloPagina = "PERFIL";
require_once("../../../assets/templates/header.php");


/*
|--------------------------------------------------------------------------
| 1. PROCESSAR O ENVIO DA FOTO ANTES DO HTML
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["foto"])) {

    header("Content-Type: application/json; charset=utf-8");

    function responderFoto($sucesso, $mensagem, $foto = null, $status = 200)
    {
        http_response_code($status);

        $resposta = [
            "sucesso" => $sucesso,
            "mensagem" => $mensagem
        ];

        if ($foto !== null) {
            $resposta["foto"] = $foto;
        }

        echo json_encode($resposta);
        exit;
    }

    // Verificar se o usuário está autenticado
    if (!isset($_SESSION["idUsuario"])) {
        responderFoto(
            false,
            "Sua sessão expirou. Faça login novamente.",
            null,
            401
        );
    }

    $idUsuario = (int) $_SESSION["idUsuario"];
    $arquivo = $_FILES["foto"];

    // Verificar erros no envio
    if ($arquivo["error"] !== UPLOAD_ERR_OK) {
        responderFoto(
            false,
            "Não foi possível receber a imagem. Tente novamente.",
            null,
            400
        );
    }

    // Limite de 2 MB
    if ($arquivo["size"] > 2 * 1024 * 1024) {
        responderFoto(
            false,
            "A imagem deve ter no máximo 2 MB.",
            null,
            400
        );
    }

    // Verificar se o arquivo recebido é realmente uma imagem
    $informacoesImagem = @getimagesize($arquivo["tmp_name"]);

    if ($informacoesImagem === false) {
        responderFoto(
            false,
            "O arquivo enviado não é uma imagem válida.",
            null,
            400
        );
    }

    // Tipos de imagem permitidos
    $tiposPermitidos = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    $tipoImagem = $informacoesImagem["mime"] ?? "";

    if (!in_array($tipoImagem, $tiposPermitidos, true)) {
        responderFoto(
            false,
            "Escolha uma imagem JPG, PNG ou WEBP.",
            null,
            400
        );
    }

    // Ler os dados binários da imagem
    $conteudoFoto = file_get_contents($arquivo["tmp_name"]);

    if ($conteudoFoto === false) {
        responderFoto(
            false,
            "Não foi possível ler a imagem enviada.",
            null,
            500
        );
    }

    try {
        $conexao = conectar();

        // Atualizar a foto do usuário no banco de dados
        $sqlFoto = "UPDATE usuarios
                    SET foto = ?
                    WHERE idUsuario = ?";

        $stmtFoto = $conexao->prepare($sqlFoto);

        if (!$stmtFoto) {
            throw new RuntimeException("Não foi possível preparar a atualização.");
        }

        // O tipo b permite enviar dados binários
        $stmtFoto->bind_param("bi", $conteudoFoto, $idUsuario);
        $stmtFoto->send_long_data(0, $conteudoFoto);

        if (!$stmtFoto->execute()) {
            throw new RuntimeException("Não foi possível atualizar a foto.");
        }

        $stmtFoto->close();

        // Retornar a imagem para atualizar a pré-visualização
        $fotoDataUri = "data:" . $tipoImagem . ";base64," .
            base64_encode($conteudoFoto);

        responderFoto(
            true,
            "Foto atualizada com sucesso!",
            $fotoDataUri
        );

    } catch (Throwable $erro) {
        error_log("Erro ao atualizar foto do perfil: " . $erro->getMessage());

        responderFoto(
            false,
            "Ocorreu um erro ao salvar a foto. Tente novamente.",
            null,
            500
        );
    }
}

/*
|--------------------------------------------------------------------------
| 2. CARREGAR A PÁGINA NORMALMENTE
|--------------------------------------------------------------------------
*/


$idUsuario = (int) $_SESSION["idUsuario"];

$sql = "SELECT
            u.idUsuario,
            u.nome,
            u.email,
            u.cpf,
            u.tipoUser,
            u.foto,
            u.materia,
            i.nome AS instituicao,
            t.serie AS turma
        FROM usuarios u
        LEFT JOIN instituicoes i
            ON i.idInstituicao = u.idInstituicao
        LEFT JOIN turmas t
            ON t.idTurma = u.idTurma
        WHERE u.idUsuario = ?";

$conexao = conectar();

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$stmt->close();

if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Preparar a foto salva no banco
$fotoDataUri = "";

if (!empty($usuario["foto"])) {
    $informacoesFoto = @getimagesizefromstring($usuario["foto"]);

    if (
        $informacoesFoto !== false &&
        in_array(
            $informacoesFoto["mime"] ?? "",
            ["image/jpeg", "image/png", "image/webp"],
            true
        )
    ) {
        $tipoFoto = $informacoesFoto["mime"];

        $fotoDataUri = "data:" . $tipoFoto . ";base64," .
            base64_encode($usuario["foto"]);
    }
}

// Carregar conquistas do aluno
$conquistas = [];

if ($usuario["tipoUser"] === "Aluno") {

    $sqlConquistas = "SELECT
                          m.titulo,
                          m.descricao,
                          c.dataConquista
                      FROM conquistas c
                      INNER JOIN metas m
                          ON m.idConquista = c.idConquista
                      WHERE c.idAluno = ?
                      ORDER BY c.dataConquista DESC";

    $stmtConquistas = $conexao->prepare($sqlConquistas);
    $stmtConquistas->bind_param("i", $idUsuario);
    $stmtConquistas->execute();

    $resultadoConquistas = $stmtConquistas->get_result();

    $conquistas = $resultadoConquistas->fetch_all(MYSQLI_ASSOC);

    $stmtConquistas->close();
}
?>

<main class="perfil-page">

    <h1 class="perfil-titulo">Perfil</h1>

    <div class="perfil-layout">

        <!-- Foto do usuário -->
        <aside class="perfil-sidebar">

            <div class="perfil-foto">

                <img
                    src="<?= $fotoDataUri !== ''
                        ? htmlspecialchars($fotoDataUri, ENT_QUOTES, 'UTF-8')
                        : '../../assets/img/perfil-padrao.jpg' ?>"
                    id="previewFoto"
                    alt="Foto de perfil"
                >

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

            <p
                id="mensagemFoto"
                class="mensagem-foto"
                role="status"
                aria-live="polite"
            ></p>

        </aside>

        <!-- Informações do usuário -->
        <section class="perfil-card">

            <div class="perfil-dados">

                <p>
                    <strong>Nome:</strong>
                    <?= htmlspecialchars(
                        $usuario["nome"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </p>

                <p>
                    <strong>CPF:</strong>
                    <?= htmlspecialchars(
                        $usuario["cpf"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </p>

                <p>
                    <strong>Email:</strong>
                    <?= htmlspecialchars(
                        $usuario["email"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </p>

                <p>
                    <strong>Instituição:</strong>
                    <?= htmlspecialchars(
                        $usuario["instituicao"] ?? "Não informada",
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </p>

                <p>
                    <strong>Atuação:</strong>
                    <?= htmlspecialchars(
                        $usuario["tipoUser"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </p>

                <?php if ($usuario["tipoUser"] === "Aluno"): ?>

                    <p>
                        <strong>Turma:</strong>
                        <?= htmlspecialchars(
                            $usuario["turma"] ?? "Não informada",
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </p>

                <?php elseif ($usuario["tipoUser"] === "Professor"): ?>

                    <p>
                        <strong>Matéria:</strong>
                        <?= htmlspecialchars(
                            $usuario["materia"] ?? "Não informada",
                            ENT_QUOTES,
                            "UTF-8"
                        ) ?>
                    </p>

                <?php endif; ?>

            </div>

            <nav class="perfil-acoes" aria-label="Ações do perfil">

                <a href="logoutPerfil.php" class="link-sair">
                    Sair
                </a>

                <a
                    href="editarPerfil.php"
                    class="link-editar"
                    title="Editar perfil"
                    aria-label="Editar perfil"
                >
                    ✎
                </a>

            </nav>

        </section>

    </div>

</main>

<script src="../../assets/js/imagem.js"></script>

<?php require_once("../../assets/templates/footer.php"); ?>