
<?php
session_start();

header("Content-Type: application/json; charset=utf-8");

function responder($sucesso, $mensagem, $foto = null, $status = 200)
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

// 1. Verificar o login
if (!isset($_SESSION["idUsuario"])) {
    responder(false, "Sessão expirada. Faça login novamente.", null, 401);
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_FILES["foto"])) {
    responder(false, "Nenhuma imagem foi recebida.", null, 400);
}

// 2. Verificar erros do upload
$arquivo = $_FILES["foto"];

if ($arquivo["error"] !== UPLOAD_ERR_OK) {
    responder(false, "Erro ao enviar a imagem.", null, 400);
}

if ($arquivo["size"] <= 0 || $arquivo["size"] > 2 * 1024 * 1024) {
    responder(false, "A imagem deve ter no máximo 2 MB.", null, 400);
}

// 3. Validar o conteúdo real da imagem
$imagem = @getimagesize($arquivo["tmp_name"]);

if ($imagem === false) {
    responder(false, "O arquivo enviado não é uma imagem válida.", null, 400);
}

$tiposPermitidos = [
    "image/jpeg",
    "image/png",
    "image/webp"
];

$tipo = $imagem["mime"] ?? "";

if (!in_array($tipo, $tiposPermitidos, true)) {
    responder(false, "Use uma imagem JPG, PNG ou WEBP.", null, 400);
}

// 4. Ler a imagem
$conteudo = file_get_contents($arquivo["tmp_name"]);

if ($conteudo === false) {
    responder(false, "Não foi possível ler a imagem.", null, 500);
}

// 5. Salvar no banco
try {
    require_once("../../config/db_connect.php");

    $conexao = conectar();

    $idUsuario = (int) $_SESSION["idUsuario"];

    $sql = "UPDATE usuarios SET foto = ? WHERE idUsuario = ?";

    $stmt = $conexao->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException("Erro ao preparar a consulta.");
    }

    $stmt->bind_param("bi", $conteudo, $idUsuario);
    $stmt->send_long_data(0, $conteudo);

    if (!$stmt->execute()) {
        throw new RuntimeException("Erro ao salvar a foto.");
    }

    // Confirmar que o usuário existe
    if ($stmt->affected_rows === 0) {
        $stmt->close();

        $verificar = $conexao->prepare(
            "SELECT foto FROM usuarios WHERE idUsuario = ?"
        );

        $verificar->bind_param("i", $idUsuario);
        $verificar->execute();
        $resultado = $verificar->get_result();
        $usuario = $resultado->fetch_assoc();
        $verificar->close();

        if (!$usuario) {
            responder(false, "Usuário não encontrado.", null, 404);
        }

        // affected_rows pode ser zero quando a foto não mudou.
        if (empty($usuario["foto"])) {
            responder(false, "A foto não foi salva no banco.", null, 500);
        }
    } else {
        $stmt->close();
    }

    // 6. Retornar a imagem salva para o JavaScript
    $fotoDataUri = "data:" . $tipo . ";base64," . base64_encode($conteudo);

    responder(true, "Foto atualizada com sucesso!", $fotoDataUri);

} catch (Throwable $erro) {
    error_log("Erro no upload da foto: " . $erro->getMessage());

    responder(
        false,
        "Não foi possível salvar a foto. Verifique o banco de dados.",
        null,
        500
    );
}
