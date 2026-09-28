<?php
$titulopagina = "CADASTRO";

require_once("../../../assets/templates/header.php");

$mensagem = "";
$conexao = conectar();

// Buscar instituições
$sqlInstituicoes = "SELECT idInstituicao, nome FROM instituicoes ORDER BY nome";
$resultadoInstituicoes = mysqli_query($conexao, $sqlInstituicoes);

// Buscar turmas
$sqlTurmas = "SELECT idTurma, serie FROM turmas ORDER BY serie";
$resultadoTurmas = mysqli_query($conexao, $sqlTurmas);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["nome"] ?? "";
    $cpf = $_POST["cpf"] ?? "";
    $dataNascimento = $_POST["dataNascimento"] ?? "";
    $email = $_POST["email"] ?? "";
    $tipoUser = $_POST["perfil"] ?? "";
    $nomeNovaTurma = $_POST["nomeNovaTurma"] ?? "";
    $idInstituicao = $_POST["idInstituicao"] ?? "";
    $idTurma = null;
    $Materia = null;

    if ($tipoUser === "Aluno") {
        $idTurma = $_POST["idTurma"] ?? null;
    }else if($tipoUser === "Professor"){
        $Materia = $_POST["Materia"] ?? null;
    }
    $conexao = conectar();

    // Verificar se a turma já existe
   if ($tipoUser === "Aluno" && $nomeNovaTurma != "") {

        $sqlTurma = "SELECT idTurma FROM turmas WHERE serie = ? AND idInstituicao = ?";
        $stmtTurma = mysqli_prepare($conexao, $sqlTurma);

        mysqli_stmt_bind_param(
            $stmtTurma,
            "si",
            $nomeNovaTurma,
            $idInstituicao
        );

        mysqli_stmt_execute($stmtTurma);

        $resultadoTurma = mysqli_stmt_get_result($stmtTurma);

        if (mysqli_num_rows($resultadoTurma) > 0) {

            $mensagem = "Esta turma já existe.";

        }else {

            $sqlNovaTurma = "INSERT INTO turmas (serie, idInstituicao) VALUES (?, ?)";

            $stmtNovaTurma = mysqli_prepare($conexao, $sqlNovaTurma);

            mysqli_stmt_bind_param(
                $stmtNovaTurma,
                "si",
                $nomeNovaTurma,
                $idInstituicao
            );

            if (mysqli_stmt_execute($stmtNovaTurma)) {

                $idTurma = mysqli_insert_id($conexao);

                $mensagem = "Turma criada com sucesso.";

            } else {

                $mensagem = "Erro ao criar a turma.";

            }

            mysqli_stmt_close($stmtNovaTurma);
        }

        mysqli_stmt_close($stmtTurma);
    }
    // Verificar se o e-mail já existe
    $sql = "SELECT idUsuario FROM usuarios WHERE email = ?";

    $stmt = mysqli_prepare($conexao, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $email
    );

    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {

        $mensagem = "Este e-mail já está cadastrado.";

    } else {

        // Cadastrar usuário
        $sql = "INSERT INTO usuarios 
        (nome, email, idGoogle, cpf, dataNascimento, tipoUser, idTurma, idInstituicao, materia) 
        VALUES (?, ?, NULL, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conexao, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sssssiis",
            $nome,
            $email,
            $cpf,
            $dataNascimento,
            $tipoUser,
            $idTurma,
            $idInstituicao,
            $materia
        );

        if (mysqli_stmt_execute($stmt)) {

            $mensagem = "Usuário cadastrado com sucesso!";

        } else {

            $mensagem = "Erro ao cadastrar usuário.";
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conexao);
}
?>
    <img src="assets/img/Qualificação TCC.svg" alt="Fundo Geométrico" class="bg-canvas">
    <main class="main-workspace">
        <div class="auth-card">
            <div class="card-header">
                <h2>Cadastrar usuário</h2>
                <p> Cadastre um usuário para que ele possa acessar o sistema com o Google.</p>
            </div>

             <?php if ($mensagem != ""): ?>
                <p><?php echo $mensagem; ?></p>
            <?php endif; ?>
            <form method="POST">
                <div class="input-group">
                    <label class="label-row" for="nome">Nome: </label>
                    <input type="text"id="nome" name="nome"placeholder="Digite o nome completo"required>
                    <label class="label-row" for="cpf">CPF: </label>
                    <input type="text"id="cpf" name="cpf"placeholder="Digite o CPF"required>
                    <label class="label-row" for="dataNascimento">Data Nascimento: </label>
                    <input type="date" id="dataNascimento" name="dataNascimento" required>
                    <label class="label-row" for="email">Email: </label>
                    <input type="email" id="email" name="email" placeholder="usuario@gmail.com" required>
                    <label class="label-row" for="instituicao">Instituição: </label>
                    <select id="instituicao" name="idInstituicao" required>
                        <option value="">Selecione uma instituição</option>

                        <?php while ($instituicao = mysqli_fetch_assoc($resultadoInstituicoes)): ?>
                            <option value="<?php echo $instituicao['idInstituicao']; ?>"><?php echo htmlspecialchars($instituicao['nome']); ?></option>
                        <?php endwhile; ?>
                    </select><br>
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
                    <label class="label-row" for="turma">Turma Pertencente</label>
                    <select id="turma" name="idTurma">
                        <option value="">Selecione uma turma</option>
                        <?php while ($turma = mysqli_fetch_assoc($resultadoTurmas)): ?>
                            <option value="<?php echo $turma['idTurma']; ?>">
                                <?php echo htmlspecialchars($turma['serie']); ?>
                            </option>
                        <?php endwhile; ?>
                        <option value="nova">+ Criar nova turma</option>
                    </select>
                    <div id="novaTurma" style="display: none;">
                        <label for="nomeNovaTurma">Nome da nova turma</label>

                        <input type="text" id="nomeNovaTurma" placeholder="Ex: 1º Ano A">
                    </div>
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
    <?php require_once("../../../assets/templates/footer.php");?>