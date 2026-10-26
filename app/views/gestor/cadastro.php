<?php
session_start();
$titulopagina = "CADASTRO";
require_once("../../../assets/templates/header.php");
$mensagem = "";
$tipoMensagem="";
$perguntarTurma = false;

$conexao = conectar();

/*
|--------------------------------------------------------------------------
| INSTITUIÇÃO DO GESTOR LOGADO
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION["idUsuario"]) ||
    !isset($_SESSION["tipoUser"]) ||
    $_SESSION["tipoUser"] !== "Gestor" ||
    empty($_SESSION["idInstituicao"])
) {
    http_response_code(403);
    exit("Acesso não autorizado ou instituição não vinculada.");
}

$idInstituicao = (int) $_SESSION["idInstituicao"];

// =====================================================
// BUSCAR TURMAS
// =====================================================
$sqlTurmas = "
    SELECT idTurma, serie
    FROM turmas
    WHERE idInstituicao = ?
    ORDER BY serie
";

$stmtTurmas = mysqli_prepare($conexao, $sqlTurmas);

mysqli_stmt_bind_param(
    $stmtTurmas,
    "i",
    $idInstituicao
);

mysqli_stmt_execute($stmtTurmas);

$resultadoTurmas = mysqli_stmt_get_result($stmtTurmas);
// =====================================================
// BUSCAR Usuarios
// =====================================================

$sqlUsuarios = "SELECT email, cpf FROM usuarios";

$resultadoUsuarios = mysqli_query(
    $conexao,
    $sqlUsuarios
);


// =====================================================
// PROCESSAR FORMULÁRIO
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST["nome"] ?? "";

    $cpf = $_POST["cpf"] ?? "";

    $dataNascimento = $_POST["dataNascimento"] ?? "";

    $email = $_POST["email"] ?? "";

    $tipoUser = $_POST["perfil"] ?? "";

    $idInstituicao = (int) $_SESSION["idInstituicao"];

    $nomeNovaTurma = trim($_POST["nomeNovaTurma"] ?? "");

    $usarTurmaExistente = $_POST["usarTurmaExistente"] ?? "nao";

    $idTurma = null;

    $materia = "";

    $turmaDuplicada = false;

    // Verifica e-mail e CPF antes de processar qualquer perfil.
    $email = trim($email);
    $cpf = preg_replace('/\D/', '', $cpf);

    $sqlEmailDuplicado = "SELECT idUsuario FROM usuarios WHERE email = ? LIMIT 1";
    $stmtEmailDuplicado = mysqli_prepare($conexao, $sqlEmailDuplicado);
    mysqli_stmt_bind_param($stmtEmailDuplicado, "s", $email);
    mysqli_stmt_execute($stmtEmailDuplicado);
    $resultadoEmailDuplicado = mysqli_stmt_get_result($stmtEmailDuplicado);
    $emailExiste = mysqli_num_rows($resultadoEmailDuplicado) > 0;
    mysqli_stmt_close($stmtEmailDuplicado);

    // Compara o CPF mesmo se estiver salvo com pontos ou traço.
    $sqlCpfDuplicado = "SELECT idUsuario FROM usuarios WHERE REPLACE(REPLACE(REPLACE(cpf, '.', ''), '-', ''), ' ', '') = ? LIMIT 1";
    $stmtCpfDuplicado = mysqli_prepare($conexao, $sqlCpfDuplicado);
    mysqli_stmt_bind_param($stmtCpfDuplicado, "s", $cpf);
    mysqli_stmt_execute($stmtCpfDuplicado);
    $resultadoCpfDuplicado = mysqli_stmt_get_result($stmtCpfDuplicado);
    $cpfExiste = mysqli_num_rows($resultadoCpfDuplicado) > 0;
    mysqli_stmt_close($stmtCpfDuplicado);

    if ($emailExiste && $cpfExiste) {
        $mensagem = "Este e-mail e este CPF já estão cadastrados.";
        $tipoMensagem = "erro";
    } elseif ($emailExiste) {
        $mensagem = "Eeste e-mail já está cadastrado.";
        $tipoMensagem = "erro";
    } elseif ($cpfExiste) {
        $mensagem = "Este CPF já está cadastrado.";
        $tipoMensagem = "erro";
    }

    // =================================================
    // DADOS DO PERFIL
    // =================================================

    if ($tipoUser === "Aluno") {

        $idTurma = $_POST["idTurma"] ?? null;

        if ($idTurma === "nova") {
            $idTurma = null;
        }

    } elseif ($tipoUser === "Professor") {

        $materia = trim($_POST["materia"] ?? "");
    }

    // =================================================
    // PROCESSAR ALUNO
    // =================================================

    if ($mensagem === "" && $tipoUser === "Aluno") {

        if ($nomeNovaTurma !== "") {

            $sqlTurma = "SELECT idTurma FROM turmas WHERE serie = ? AND idInstituicao = ?";
            $stmtTurma = mysqli_prepare(
                $conexao,
                $sqlTurma
            );

            mysqli_stmt_bind_param($stmtTurma,"si",$nomeNovaTurma, $idInstituicao);

            mysqli_stmt_execute($stmtTurma);

            $resultadoTurma = mysqli_stmt_get_result($stmtTurma);


            // =============================================
            // TURMA JÁ EXISTE
            // =============================================

            if (mysqli_num_rows($resultadoTurma) > 0) {

                $turma =mysqli_fetch_assoc($resultadoTurma);

                $idTurma = (int) $turma["idTurma"];

                $turmaDuplicada = true;


                // Se ainda não confirmou SIM
                if (
                    $usarTurmaExistente !== "sim"
                ) {

                    $perguntarTurma = true;
                }


            } else {

                // =========================================
                // TURMA NÃO EXISTE → CRIAR
                // =========================================

                $sqlNovaTurma =
                    "INSERT INTO turmas
                    (serie, idInstituicao)
                    VALUES (?, ?)";

                $stmtNovaTurma =
                    mysqli_prepare(
                        $conexao,
                        $sqlNovaTurma
                    );

                mysqli_stmt_bind_param(
                    $stmtNovaTurma,
                    "si",
                    $nomeNovaTurma,
                    $idInstituicao
                );


                if (
                    mysqli_stmt_execute(
                        $stmtNovaTurma
                    )
                ) {

                    $idTurma =(int) mysqli_insert_id($conexao);

                } else {
                    $mensagem ="Erro ao criar a turma: " .mysqli_stmt_error($stmtNovaTurma);
                    $tipoMensagem = "erro";
                }


                mysqli_stmt_close(
                    $stmtNovaTurma
                );
            }


            mysqli_stmt_close(
                $stmtTurma
            );
        }


        // =================================================
        // CADASTRAR ALUNO
        // =================================================

        if (
            $mensagem === "" &&
            !$perguntarTurma
        ) {


            // =============================================
            // VERIFICAR TURMA
            // =============================================

            if (
                $idTurma === null ||
                $idTurma === ""
            ) {

                $mensagem ="Selecione uma turma.";
                    $tipoMensagem = "erro";

            } else {

                $idTurma =
                    (int) $idTurma;


                // =========================================
                // CONFIRMAR QUE A TURMA EXISTE
                // =========================================

                $sqlVerificaTurma =
                    "SELECT idTurma
                     FROM turmas
                     WHERE idTurma = ?
                     AND idInstituicao = ?";

                $stmtVerificaTurma =
                    mysqli_prepare(
                        $conexao,
                        $sqlVerificaTurma
                    );

                mysqli_stmt_bind_param(
                    $stmtVerificaTurma,
                    "ii",
                    $idTurma,
                    $idInstituicao
                );

                mysqli_stmt_execute(
                    $stmtVerificaTurma
                );

                $resultadoVerificaTurma =
                    mysqli_stmt_get_result(
                        $stmtVerificaTurma
                    );


                if (
                    mysqli_num_rows(
                        $resultadoVerificaTurma
                    ) === 0
                ) {

                    $mensagem = "Turma selecionada não existe.";
                    $tipoMensagem = "erro";

                } else {


                    // =====================================
                    // VERIFICAR E-MAIL
                    // =====================================

                    $sqlEmail =
                        "SELECT idUsuario
                         FROM usuarios
                         WHERE email = ?";

                    $stmtEmail =
                        mysqli_prepare(
                            $conexao,
                            $sqlEmail
                        );

                    mysqli_stmt_bind_param(
                        $stmtEmail,
                        "s",
                        $email
                    );

                    mysqli_stmt_execute(
                        $stmtEmail
                    );

                    $resultadoEmail =
                        mysqli_stmt_get_result(
                            $stmtEmail
                        );


                    if (
                        mysqli_num_rows(
                            $resultadoEmail
                        ) > 0
                    ) {

                        $mensagem ="Este e-mail já está cadastrado.";
                        $tipoMensagem = "erro";

                    } else {


                        // =================================
                        // CADASTRAR ALUNO
                        // =================================

                        $sqlInsert =
                            "INSERT INTO usuarios
                            (
                                nome,
                                email,
                                idGoogle,
                                cpf,
                                dataNascimento,
                                tipoUser,
                                idTurma,
                                idInstituicao,
                                materia
                            )
                            VALUES
                            (?, ?, NULL, ?, ?, ?, ?, ?, ?)";


                        $stmtInsert =
                            mysqli_prepare(
                                $conexao,
                                $sqlInsert
                            );


                        mysqli_stmt_bind_param(
                            $stmtInsert,
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


                        if (
                            mysqli_stmt_execute(
                                $stmtInsert
                            )
                        ) {

                            if ($turmaDuplicada) {

                                $mensagem ="Aluno cadastrado na turma existente!";
                                $tipoMensagem = "sucesso";

                            } else {

                                $mensagem ="Aluno cadastrado com sucesso!";
                                $tipoMensagem = "sucesso";
                            }

                        } else {

                            $mensagem ="Erro ao cadastrar usuário: " .mysqli_stmt_error($stmtInsert);
                        }


                        mysqli_stmt_close(
                            $stmtInsert
                        );
                    }


                    mysqli_stmt_close(
                        $stmtEmail
                    );
                }


                mysqli_stmt_close(
                    $stmtVerificaTurma
                );
            }
        }
    }


    // =================================================
    // PROFESSOR / GESTÃO
    // =================================================

    if ($mensagem === "" &&$tipoUser !== "Aluno") {


        // =============================================
        // PROFESSOR
        // =============================================

        if ($tipoUser === "Professor") {

            $materia =
                trim(
                    $_POST["materia"] ?? ""
                );


            if ($materia === "") {

                $mensagem ="Informe a matéria ministrada.";
                    $tipoMensagem = "erro";
            }
        }


        // =============================================
        // CADASTRAR PROFESSOR / GESTÃO
        // =============================================

        if ($mensagem === "") {

            $idTurma = null;


            // =========================================
            // VERIFICAR E-MAIL
            // =========================================

            $sqlEmail =
                "SELECT idUsuario
                 FROM usuarios
                 WHERE email = ?";

            $stmtEmail =
                mysqli_prepare(
                    $conexao,
                    $sqlEmail
                );

            mysqli_stmt_bind_param(
                $stmtEmail,
                "s",
                $email
            );

            mysqli_stmt_execute(
                $stmtEmail
            );

            $resultadoEmail =
                mysqli_stmt_get_result(
                    $stmtEmail
                );


            if (
                mysqli_num_rows(
                    $resultadoEmail
                ) > 0
            ) {

                $mensagem ="Este e-mail já está cadastrado.";
                $tipoMensagem = "erro";

            } else {


                // =====================================
                // CADASTRAR PROFESSOR / GESTÃO
                // =====================================

                $sqlInsert =
                    "INSERT INTO usuarios
                    (
                        nome,
                        email,
                        idGoogle,
                        cpf,
                        dataNascimento,
                        tipoUser,
                        idTurma,
                        idInstituicao,
                        materia
                    )
                    VALUES
                    (?, ?, NULL, ?, ?, ?, ?, ?, ?)";


                $stmtInsert =
                    mysqli_prepare(
                        $conexao,
                        $sqlInsert
                    );


                mysqli_stmt_bind_param(
                    $stmtInsert,
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


                if (mysqli_stmt_execute($stmtInsert)) {

                    $mensagem = "Usuário cadastrado com sucesso!";
                        $tipoMensagem = "sucesso";

                } else {

                    $mensagem ="Erro ao cadastrar usuário: " .mysqli_stmt_error($stmtInsert);
                        $tipoMensagem = "erro";
                }


                mysqli_stmt_close(
                    $stmtInsert
                );
            }


            mysqli_stmt_close(
                $stmtEmail
            );
        }
    }
}

?>

<!-- ================================================= -->
<!-- FUNDO -->
<!-- ================================================= -->

<img
    src="/mariag/PROJETO/assets/img/Qualificação TCC.svg"
    alt="Fundo Geométrico"
    class="bg-canvas"
>


<!-- ================================================= -->
<!-- CONTEÚDO -->
<!-- ================================================= -->

<main class="main-workspace">

    <div class="auth-card">


        <!-- CABEÇALHO -->

        <div class="card-header">

            <h2>Cadastrar usuário</h2>

            <p>
                Cadastre um usuário para que ele possa acessar
                o sistema com o Google.
            </p>

        </div>


        


        <!-- ================================================= -->
        <!-- FORMULÁRIO -->
        <!-- ================================================= -->

        <form method="POST">


            <!-- CONTROLE DA CONFIRMAÇÃO -->

            <input type="hidden"id="usarTurmaExistente"name="usarTurmaExistente"value="<?php echo htmlspecialchars($_POST["usarTurmaExistente"] ?? "nao");?>">


            <div class="input-group">
                <label class="label-row"for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" placeholder="Digite o nome completo" value="<?php echo htmlspecialchars($_POST["nome"] ?? "");?>"required>

                <label class="label-row" for="cpf"> CPF:</label>
                <input type="text"id="cpf"name="cpf"placeholder="Digite o CPF"value="<?php echo htmlspecialchars($_POST["cpf"] ?? "");?>"required>

                <label class="label-row"for="dataNascimento">Data Nascimento:</label>
                <input type="date" id="dataNascimento" name="dataNascimento" value="<?php echo htmlspecialchars($_POST["dataNascimento"] ?? "");?>"required>

                <label class="label-row" for="email">Email:</label>
                <input type="email" id="email"name="email" placeholder="usuario@gmail.com" value="<?php echo htmlspecialchars($_POST["email"] ?? "" );?>"required>

                <label>Tipo de perfil</label>

            </div>

            <div class="radio-flex">

                <label class="radio-label">
                    <input type="radio" name="perfil" value="Aluno"<?php if (($_POST["perfil"] ?? "Aluno") === "Aluno") { echo "checked";}?>>Aluno</label>

                <label class="radio-label">
                    <input type="radio" name="perfil" value="Gestor" <?php if (($_POST["perfil"] ?? "Gestor" ) === "Gestor") {echo "checked"; } ?>>Gestão</label>

                <label class="radio-label">
                    <input type="radio"name="perfil" value="Professor"<?php if (( $_POST["perfil"] ?? "Professor" ) === "Professor") {echo "checked";} ?>> Professor</label>

            </div>

            <div class="input-group">

                <!-- ALUNO -->

                <div id="perguntaAluno"class="pergunta">

                    <label class="label-row" for="turma">Turma Pertencente </label>


                    <select id="turma" name="idTurma" >

                        <option value=""> Selecione uma turma </option>

                        <?php while ( $turma = mysqli_fetch_assoc( $resultadoTurmas)): ?>

                            <option value=" <?php echo $turma["idTurma"]; ?>"
                             <?php if (( $_POST["idTurma"] ?? "") == $turma["idTurma"]) {
                                echo "selected";
                            }?>>
                                <?php echo htmlspecialchars($turma["serie"]);?>
                            </option>

                        <?php endwhile; ?>


                        <option value="nova" <?php if (($_POST["idTurma"] ?? "") === "nova") {echo "selected";}?>>
                            + Criar nova turma
                        </option>

                    </select>


                    <!-- NOVA TURMA -->

                    <div id="novaTurma" style=" display: <?php echo (($_POST["idTurma"] ?? "") === "nova" ||$perguntarTurma)? "block":  "none"; ?>;">
                       
                        <label for="nomeNovaTurma">Nome da nova turma</label>
                        <input type="text"id="nomeNovaTurma"name="nomeNovaTurma"placeholder="Ex: 1º Ano A" value="<?php echo htmlspecialchars($_POST["nomeNovaTurma"] ?? "");?>">

                    </div>

                </div>

                <!-- PROFESSOR -->
                <div id="perguntaProfessor"class="pergunta">
                    <label class="label-row"for="materia">Matéria Ministrada</label>
                    <input type="text"id="materia"name="materia"value="<?php echo htmlspecialchars($_POST["materia"] ?? "");?>">
                </div>

                <button type="submit"class="btn-submit">Cadastrar</button>
                <!-- MENSAGEM -->
                <?php if (!empty($mensagem)): ?>
                    <div class="mensagem <?= $tipoMensagem ?>">
                        <?= htmlspecialchars($mensagem) ?>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</main>

<!-- CONFIRMAÇÃO DE TURMA -->

<?php if ($perguntarTurma): ?>

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        const resposta = confirm(
            "Esta turma já existe.\n\n" +
            "Deseja inserir o aluno nela?"
        );


        if (resposta) {
            document.getElementById(
                "usarTurmaExistente"
            ).value = "sim";


            document.querySelector(
                "form"
            ).submit();


        } else {

            const turma =
                document.getElementById(
                    "turma"
                );

            const novaTurma =
                document.getElementById(
                    "novaTurma"
                );

            const nomeNovaTurma =
                document.getElementById(
                    "nomeNovaTurma"
                );


            // Voltar para seleção de turma

            turma.value = "";

            novaTurma.style.display =
                "none";

            nomeNovaTurma.value = "";

            turma.focus();
        }

    }
);

</script>

<?php endif; ?>


<?php

require_once(
    "../../../assets/templates/footer.php"
);

?>