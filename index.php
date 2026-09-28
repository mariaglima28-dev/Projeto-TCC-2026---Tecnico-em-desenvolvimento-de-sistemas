<?php
$titulopagina = "AprimorAI";
require_once("assets/templates/header.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styleIndex.css">
</head>

<body>
    <div class="banner">
        <img src="assets/img/AprimorAI.svg" style="width: 20%; height: auto;">
    </div>
    <div class="banner-branco">
        <div class="auth-buttons">
            <a href="login.php" class="btn btn-primary">Login</a>
        </div>
    </div>
    <section class="hero">
        <img src="assets/img/landing-page.svg" class="imagem-esq">
        <div class="hero-content">
            <span class="badge">✨ O Futuro do Aprendizado</span>
            <h1>Potencialize seus estudos com <span>Inteligência Artificial</span></h1>
            <p>O AprimorAI personaliza seu plano de estudos, tira dúvidas em tempo real e acompanha sua evolução em uma
                experiência interativa e adaptativa.</p>
            <div class="hero-actions">
                <a href="login.php" class="btn btn-secondary btn-lg">Já tenho conta</a>
            </div>
        </div>

        <div class="hero-graphic">
            <div class="chat-preview-card">
                <div class="chat-bubble user">
                    <p>Como aplicar a fórmula de Taylor nesta questão?</p>
                </div>
                <div class="chat-bubble ai">
                    <div class="ai-header">
                        <span class="sparkle">🪄</span> <strong>AprimorAI</strong>
                    </div>
                    <p>Vamos resolver juntos! O primeiro passo é encontrar as derivadas da função no ponto escolhido...
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="recursos" class="features">
        <div class="section-header">
            <span class="section-badge">Recursos Inteligentes</span>
            <h2>Tudo o que você precisa para <span>evoluir nos estudos</span></h2>
            <p>Tecnologia adaptativa e recursos avançados desenhados para acelerar seu aprendizado de forma simples e
                intuitiva.</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="card-header-flex">
                    <div class="icon-wrapper">
                        <span>🎯</span>
                    </div>
                    <span class="card-tag">Adaptativo</span>
                </div>
                <h3>Estudo Personalizado</h3>
                <p>Algoritmos de IA que identificam suas lacunas de conhecimento e criam cronogramas sob medida para sua
                    rotina.</p>
                <div class="card-footer">
                    <span class="card-link">Plano sob medida <span class="arrow">&rarr;</span></span>
                </div>
            </div>

            <div class="feature-card featured">
                <div class="card-header-flex">
                    <div class="icon-wrapper">
                        <span>⚡</span>
                    </div>
                    <span class="card-tag tag-primary">Tempo Real</span>
                </div>
                <h3>Tutoria 24/7</h3>
                <p>Tire suas dúvidas instantaneamente com explicações passo a passo e contexto adaptado a qualquer hora
                    do dia.</p>
                <div class="card-footer">
                    <span class="card-link">Respostas em segundos <span class="arrow">&rarr;</span></span>
                </div>
            </div>

            <div class="feature-card">
                <div class="card-header-flex">
                    <div class="icon-wrapper">
                        <span>📊</span>
                    </div>
                    <span class="card-tag">Métricas</span>
                </div>
                <h3>Análise de Desempenho</h3>
                <p>Relatórios visuais e estatísticas precisas que mostram a sua evolução contínua em cada disciplina.
                </p>
                <div class="card-footer">
                    <span class="card-link">Acompanhe seu progresso <span class="arrow">&rarr;</span></span>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-banner">
        <div class="cta-container">
            <h2>Pronto para transformar sua rotina de estudos?</h2>
            <p>Crie sua conta gratuitamente e experimente uma nova forma de aprender.</p>
            <div class="cta-buttons">
                <a href="explicar.html" class="btn btn-primary btn-lg">Conheça a metodologia</a>
                <a href="login.php" class="btn btn-outline-white btn-lg">Fazer Login</a>
            </div>
        </div>
    </section>
    <footer>
        <img src="assets/img/footer1.svg" class="imagem-canto-esq">
        <img src="assets/img/footer2.svg" class="imagem-canto-dir">
        <h3>Desenvolvido por Danielle Alves Lima, Maria Gabriela Pieri de Lima e Samuel Silva Tironi</h3>
    </footer>
</body>

</html>