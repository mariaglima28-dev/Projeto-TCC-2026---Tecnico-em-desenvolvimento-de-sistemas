document.addEventListener("DOMContentLoaded", function () {

    // Código que já controla Aluno/Professor
    const radios = document.querySelectorAll('input[name="perfil"]');
    const perguntas = document.querySelectorAll(".pergunta");

    function verificarPerfil() {

        const selecionado = document.querySelector(
            'input[name="perfil"]:checked'
        );

        if (!selecionado) {
            return;
        }

        perguntas.forEach(function (div) {
            div.style.display = "none";
        });

        const div = document.getElementById(
            "pergunta" + selecionado.value
        );

        if (div) {
            div.style.display = "block";
        }
    }

    radios.forEach(function (radio) {
        radio.addEventListener("change", verificarPerfil);
    });

    verificarPerfil();


    // NOVA TURMA
    const turma = document.getElementById("turma");
    const novaTurma = document.getElementById("novaTurma");

    if (turma) {

        turma.addEventListener("change", function () {

            if (turma.value === "nova") {
                novaTurma.style.display = "block";
            } else {
                novaTurma.style.display = "none";
            }

        });

    }

});