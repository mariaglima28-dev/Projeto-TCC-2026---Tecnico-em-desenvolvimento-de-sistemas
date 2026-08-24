const botaoFiltrar = document.querySelector("#botaoFiltrar");
const botaoLimpar = document.querySelector("#botaoLimpar");

const filtrar = () => {

    const materia = document.querySelector("#filtroMateria").value;
    const data = document.querySelector("#filtroData").value;

    const atividades = document.querySelectorAll(".atividade");

    atividades.forEach(atividade => {

        const materiaAtividade = atividade.dataset.materia;
        const dataAtividade = atividade.dataset.data;

        let mostrar = true;

        // Filtrar por matéria
        if (materia !== "" && materiaAtividade !== materia) {
            mostrar = false;
        }

        // Filtrar por data
        if (data !== "" && dataAtividade !== data) {
            mostrar = false;
        }

        if (mostrar) {
            atividade.style.display = "block";
        } else {
            atividade.style.display = "none";
        }

    });
};

botaoFiltrar.addEventListener("click", filtrar);


botaoLimpar.addEventListener("click", () => {

    const atividades = document.querySelectorAll(".atividade");

    atividades.forEach(atividade => {
        atividade.style.display = "block";
    });

    document.querySelector("#filtroMateria").value = "";
    document.querySelector("#filtroData").value = "";
});