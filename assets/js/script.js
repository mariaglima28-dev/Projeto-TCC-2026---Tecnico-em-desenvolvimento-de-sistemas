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


.then(res => res.text())
.then(resposta => {

    console.log("1 - Resposta do PHP:", resposta);

    try {

        const dados = JSON.parse(resposta);

        console.log("2 - JSON convertido:", dados);

        console.log("3 - Tipo recebido:", dados.tipoUser);

        const tipo = dados.tipoUser.trim().toLowerCase();

        console.log("4 - Tipo tratado:", tipo);

        if (tipo === "aluno") {
            window.location.href ="app/views/aluno/index.php";

        } else if (tipo === "professor") {
            window.location.href ="app/views/professor/index.php";

        } else if (tipo === "gestor") {
            window.location.href ="app/views/gestor/index.php";

        } else {
            alert("Tipo de usuário não identificado: [" + dados.tipoUser + "]");

        }

    } catch (erro) {

        console.error("ERRO REAL:", erro);
        console.error("Resposta recebida:", resposta);

        alert("Erro ao processar a resposta do servidor.");

    }

})
.catch(erro => {

    console.error("ERRO NO FETCH:", erro);

});
}