<?php

session_start();

header('Content-Type: application/json');

require_once("config/db_connect.php");


// Receber token enviado pelo JavaScript
$dados = json_decode(
    file_get_contents("php://input"),
    true
);


if (!isset($dados['token'])) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Token do Google não recebido."
    ]);

    exit;
}


$token = $dados['token'];


// Verificar token com o Google
$url = "https://oauth2.googleapis.com/tokeninfo?id_token="
     . urlencode($token);


$resposta = file_get_contents($url);


if ($resposta === false) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Não foi possível verificar o login com o Google."
    ]);

    exit;
}


$google = json_decode($resposta, true);


// Verificar se o token possui os dados necessários
if (
    !isset($google['sub']) ||
    !isset($google['email'])
) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Token do Google inválido."
    ]);

    exit;
}


// ID único da conta Google
$idGoogle = $google['sub'];


// E-mail da conta Google
$email = $google['email'];


// Client ID do seu projeto Google
$clientId =
"949242625796-3tqep1q9beie1cn0ivjd32i7o7isb6kn.apps.googleusercontent.com";


// Verificar se o token pertence ao seu aplicativo
if (
    !isset($google['aud']) ||
    $google['aud'] !== $clientId
) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Aplicativo Google inválido."
    ]);

    exit;
}


$conexao = conectar();


// ==================================================
// 1. PRIMEIRO: procurar pelo idGoogle
// ==================================================

$sql = "SELECT * FROM usuarios WHERE idGoogle = ?";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $idGoogle
);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

$usuario = mysqli_fetch_assoc($resultado);


if ($usuario) {

    // Usuário já vinculou essa conta Google
    $_SESSION['idUsuario'] = $usuario['idUsuario'];
    $_SESSION['tipoUser'] = $usuario['tipoUser'];
    $_SESSION['usuarioLogado'] = true;
    $_SESSION['nomeUsuario'] = $usuario['nome'];


    echo json_encode([
        'sucesso' => true,
        'tipoUser' => $usuario['tipoUser']
        
    ]);


    mysqli_stmt_close($stmt);
    mysqli_close($conexao);

    exit;
}


mysqli_stmt_close($stmt);


// ==================================================
// 2. NÃO encontrou idGoogle
//    Agora procurar pelo e-mail
// ==================================================

$sql = "SELECT * FROM usuarios WHERE email = ?";

$stmt = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $email
);

mysqli_stmt_execute($stmt);

$resultado = mysqli_stmt_get_result($stmt);

$usuario = mysqli_fetch_assoc($resultado);


// ==================================================
// 3. E-mail não cadastrado
// ==================================================

if (!$usuario) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Usuário não cadastrado. Procure o gestor."
    ]);

    mysqli_stmt_close($stmt);
    mysqli_close($conexao);

    exit;
}


// ==================================================
// 4. E-mail já possui outro Google vinculado
// ==================================================

if (
    !empty($usuario['idGoogle']) &&
    $usuario['idGoogle'] !== $idGoogle
) {

    echo json_encode([
        "sucesso" => false,
        "mensagem" => "Este e-mail já está vinculado a outra conta Google."
    ]);

    mysqli_stmt_close($stmt);
    mysqli_close($conexao);

    exit;
}


// ==================================================
// 5. Primeiro login do usuário
//    Vincular idGoogle ao cadastro existente
// ==================================================

$sql = "UPDATE usuarios
        SET idGoogle = ?
        WHERE idUsuario = ?";

$stmtUpdate = mysqli_prepare($conexao, $sql);

mysqli_stmt_bind_param(
    $stmtUpdate,
    "si",
    $idGoogle,
    $usuario['idUsuario']
);

mysqli_stmt_execute($stmtUpdate);


// ==================================================
// 6. Criar sessão
// ==================================================

if ($usuario) {

    $_SESSION['usuarioLogado'] = $usuario['nome'];
    $_SESSION['idUsuario'] = $usuario['idUsuario'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['tipoUser'] = $usuario['tipoUser'];
    $_SESSION['idTurma'] = $usuario['idTurma'];
    $_SESSION['idInstituicao'] = $usuario['idInstituicao'];

    echo json_encode([
        "sucesso" => true,
        "usuarioLogado" => $usuario['nome'],
        "tipoUser" => $usuario['tipoUser']
    ]);

    mysqli_stmt_close($stmt);
    mysqli_close($conexao);
    exit;
}


mysqli_stmt_close($stmt);
mysqli_stmt_close($stmtUpdate);
mysqli_close($conexao);

?>