document.addEventListener("DOMContentLoaded", () => {
    const botaoFiltrar = document.querySelector("#botaoFiltrar");
    const botaoLimpar = document.querySelector("#botaoLimpar");
    const filtroMateria = document.querySelector("#filtroMateria");
    const filtroData = document.querySelector("#filtroData");
    const filtroNome = document.querySelector("#filtroNome");

    function filtrar() {
        const nome = filtroNome.value.trim().toLowerCase();
        const materia = filtroMateria.value;
        const data = filtroData.value;

        document.querySelectorAll(".atividade").forEach((atividade) => {
            const titulo = atividade.querySelector("h2")
                .textContent.trim().toLowerCase();

            const materiaAtividade = atividade.dataset.materia;
            const dataAtividade = atividade.dataset.data.split(" ")[0];

            const correspondeNome =
                nome === "" || titulo.includes(nome);

            const correspondeMateria =
                materia === "" || materiaAtividade === materia;

            const correspondeData =
                data === "" || dataAtividade === data;

            atividade.hidden = !(
                correspondeNome &&
                correspondeMateria &&
                correspondeData
            );
        });
    }

    botaoFiltrar?.addEventListener("click", filtrar);

    filtroNome?.addEventListener("input", filtrar);

    botaoLimpar?.addEventListener("click", () => {
        filtroNome.value = "";
        filtroMateria.value = "";
        filtroData.value = "";

        document.querySelectorAll(".atividade").forEach((atividade) => {
            atividade.hidden = false;
        });
    });
});