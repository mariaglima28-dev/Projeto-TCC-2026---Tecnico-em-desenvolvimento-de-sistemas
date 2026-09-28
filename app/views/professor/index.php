<?php
$tituloPagina="Home";
require_once("../../../assets/templates/header.php");

?>
<nav class="navbar">
    <a href="#">Quiz</a>
    <a href="#">Atividades</a>
    <a href="#">Desempenho</a>
</nav>

<div class="main-container">
    <h1>Home</h1>
        
    <div class="dashboard-panel">
        <div class="welcome-banner">Bem-vindo(a), <?php ?>!<br>
            Pronto para gerar quizzes, atividades e dentre outros?
        </div>

        <div class="cards-container">
            <div class="card card-blue">
                <h3>Atividades geradas</h3>
                <p>Atividade avaliativa de Biologia</p>
            </div>

            <div class="card card-pink">
                <h3>Último quiz gerado</h3>
                <p>Matéria: Biologia<br>Feedback: Alunos com dificuldades</p>
            </div>

            <div class="card card-orange">
                <h3>Desempenho</h3>
                <p>30% das salas estão abaixo do básico</p>
            </div>

        </div>
    </div>
</div>
