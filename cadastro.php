<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Qualificação TCC</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <img src="assets/img/Qualificação TCC.svg" alt="Fundo Geométrico" class="bg-canvas">
    <main class="main-workspace">
        <div class="auth-card">
            <div class="card-header">
                <h2>Cadastro</h2>
                <p>Crie as credenciais para acessar o sistema.</p>
            </div>

            <form method="POST">
                <div class="input-group">
                    <label class='label-row' for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required><br><br>
                    <label class='label-row'for="cpf">CPF(apenas números)</label>
                    <input typ="number" id="cpf" name="cpf" placeholder="000.000.000-00" required><br><br>
                    <label class='label-row'for="dataNasc">Data de Nascimento</label>
                    <input type="date" id="dataNasc" required><br><br>
                    <label class='label-row' for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="nome@exemplo.com" required><br><br>
                    <label class='label-row' for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Crie uma senha forte" required><br><br>
                    <label class='label-row' for="confirmar">Confirmação de Senha</label>
                    <input type="password" id="confirmar" name="confirma_senha" placeholder="Repita a senha criada" required><br><br>
                    <label class='label-row'for="instituicao">Instituição</label>
                    <input typ="number" id="cpf"  placeholder="Escola Teste"><br><br>
                    <label>Tipo de perfil</label>
                </div>
                <div class="radio-flex">
                    <label class="radio-label"><input type="radio" name="perfil" value="Aluno" checked>Aluno</label>
                    <label class="radio-label"><input type="radio" name="perfil" value="Gestão">Gestão</label>
                    <label class="radio-label"><input type="radio" name="perfil" value="Professor">Professor</label>
                </div>
                <div class="input-group">
                <!-- Extra Aluno -->
                <div id="perguntaAluno" class="pergunta">
                    <label class='label-row' for="siape">Turma Pertencente</label>
                    <input type="text" id="turma">
                </div>

                <!-- Extra Professor -->
                <div id="perguntaProfessor" class="pergunta">
                    <label class='label-row' for="siape">Matéria Ministrada</label>
                    <input type="text" id="materia">
                </div>

                <button type="submit" class="btn-submit">Cadastrar</button></div> 
                    </form>
                </div>
            </section>
        </div>
        <textarea><?php echo $mensagem ?></textarea>
    </main>
    <script src="assets/js/script.js"></script>
</body>
</html>