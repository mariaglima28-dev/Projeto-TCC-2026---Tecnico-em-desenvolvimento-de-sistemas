const radios = document.querySelectorAll('input[name="perfil"]');
const pergunta = document.querySelectorAll(".pergunta");

function verificarPerfil() {

    const selecionado = document.querySelector('input[name="perfil"]:checked');

    if (!selecionado) return;

    pergunta.forEach(div => div.style.display = "none");

    const div = document.getElementById("pergunta" + selecionado.value);

    if (div) {
        div.style.display = "block";
    }
}

radios.forEach(radio =>
    radio.addEventListener("change", verificarPerfil)
);

verificarPerfil();

function handleCredentialResponse(response) {

    const token = response.credential;

    fetch("loginGoogle.php", {

        method: "POST",

        headers: {
            "Content-Type": "application/json"
        },

        body: JSON.stringify({
            token: token
        })

    })
    .then(res => res.json())
    .then(dados => {

        if (dados.sucesso) {

            window.location = "index.php";

        } else {

            alert("Erro ao fazer login.");

        }

    });

}
<?php

session_start();

require_once 'conexao.php';

$email = $_POST['email'] ?? '';

$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Usuário não cadastrado.");
}

$usuario = $resultado->fetch_assoc();

$_SESSION['usuario_id'] = $usuario['id'];
$_SESSION['nome'] = $usuario['nome'];
$_SESSION['email'] = $usuario['email'];
$_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

switch ($usuario['tipo_usuario']) {

    case 'professor':
        header("Location: professor.php");
        break;

    case 'gestor':
        header("Location: gestor.php");
        break;

    case 'aluno':
        header("Location: pagiina-aluno.php");
        break;

    default:
        die("Tipo de usuário inválido.");
}

exit;
handleCredentialResponse()