<?php
require_once 'db.php';

$acao = $_GET['action'] ?? $_POST['action'] ?? 'obter_dados';

switch ($acao) {
    case 'obter_dados':
        obterDadosTurmasEAlunos($pdo);
        break;
    case 'enviar_boletins':
        enviarBoletinsPorEmail($pdo);
        break;
    default:
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'erro' => 'Ação inválida.']);
        break;
}

function obterDadosTurmasEAlunos($pdo) {
    $periodo = $_GET['periodo'] ?? '3B';
    
    $turmas = $pdo->query("SELECT id, nome FROM turmas")->fetchAll();
    $resultado = [];

    foreach ($turmas as $t) {
        $stmtAlunos = $pdo->prepare("SELECT id, nome, ra, email, frequencia_pct FROM alunos WHERE turma_id = :turma_id");
        $stmtAlunos->execute([':turma_id' => $t['id']]);
        $alunos = $stmtAlunos->fetchAll();

        $listaAlunos = [];
        foreach ($alunos as $a) {
            $stmtNotas = $pdo->prepare("SELECT materia, nota FROM notas WHERE aluno_id = :aluno_id AND bimestre = :periodo");
            $stmtNotas->execute([':aluno_id' => $a['id'], ':periodo' => $periodo]);
            $notas = $stmtNotas->fetchAll(PDO::FETCH_KEY_PAIR);

            $listaAlunos[] = [
                'id' => (int)$a['id'],
                'nome' => $a['nome'],
                'ra' => $a['ra'],
                'email' => $a['email'],
                'freq' => round((float)$a['frequencia_pct'], 1) . '%',
                'notas' => array_map('floatval', $notas)
            ];
        }

        $resultado[$t['id']] = [
            'nomeTurma' => $t['nome'],
            'alunos' => $listaAlunos
        ];
    }

    echo json_encode(['sucesso' => true, 'dados' => $resultado], JSON_UNESCAPED_UNICODE);
}

function enviarBoletinsPorEmail($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);

    $alunoIds = $input['aluno_ids'] ?? [];
    $periodo = $input['periodo'] ?? '3º Bimestre';
    $parecer = $input['parecer'] ?? '';

    if (empty($alunoIds)) {
        http_response_code(400);
        echo json_encode(['sucesso' => false, 'erro' => 'Nenhum aluno selecionado.']);
        return;
    }

    $sucessos = 0;
    $falhas = 0;

    foreach ($alunoIds as $alunoId) {
        $stmt = $pdo->prepare("SELECT a.*, t.nome as nome_turma FROM alunos a JOIN turmas t ON a.turma_id = t.id WHERE a.id = :id");
        $stmt->execute([':id' => $alunoId]);
        $aluno = $stmt->fetch();

        if (!$aluno) continue;

        $stmtNotas = $pdo->prepare("SELECT materia, nota FROM notas WHERE aluno_id = :aluno_id");
        $stmtNotas->execute([':aluno_id' => $alunoId]);
        $notas = $stmtNotas->fetchAll(PDO::FETCH_KEY_PAIR);

        // Montagem do e-mail HTML
        $assunto = "Boletim Escolar ({$periodo}) - " . $aluno['nome'];
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: AprimorAI <nao-responder@aprimorai.edu.br>\r\n";

        $corpo = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>";
        $corpo .= "<h2 style='color: #c0576f;'>AprimorAI - Boletim Escolar</h2>";
        $corpo .= "<p>Olá, <strong>{$aluno['nome']}</strong> (RA: {$aluno['ra']})</p>";
        $corpo .= "<p>Turma: {$aluno['nome_turma']} | Frequência: {$aluno['frequencia_pct']}%</p>";
        $corpo .= "<h3>Notas:</h3><ul>";
        foreach ($notas as $materia => $nota) {
            $corpo .= "<li><strong>{$materia}:</strong> " . number_format($nota, 1) . "</li>";
        }
        $corpo .= "</ul>";
        $corpo .= "<p><em>Parecer Pedagógico:</em> {$parecer}</p>";
        $corpo .= "</div>";

        if (mail($aluno['email'], $assunto, $corpo, $headers)) {
            $sucessos++;
        } else {
            $falhas++;
        }
    }

    echo json_encode([
        'sucesso' => true,
        'mensagem' => "Processo concluído. {$sucessos} e-mails enviados, {$falhas} falhas."
    ], JSON_UNESCAPED_UNICODE);
}