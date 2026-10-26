<?php
require_once 'db.php';

$etapa = $_GET['etapa'] ?? 'todas';
$periodo = $_GET['periodo'] ?? '3B';

try {
    $sqlTurmas = "SELECT t.id, t.nome, t.etapa, t.etapa_nome, t.observacao,
                         COUNT(DISTINCT a.id) as alunos,
                         AVG(a.frequencia_pct) as frequencia_media
                  FROM turmas t
                  LEFT JOIN alunos a ON a.turma_id = t.id";

    if ($etapa !== 'todas') {
        $sqlTurmas .= " WHERE t.etapa = :etapa";
    }

    $sqlTurmas .= " GROUP BY t.id";

    $stmt = $pdo->prepare($sqlTurmas);
    if ($etapa !== 'todas') {
        $stmt->bindValue(':etapa', $etapa);
    }
    $stmt->execute();
    $turmasRaw = $stmt->fetchAll();

    $turmasProcessadas = [];
    $somaMediasGerais = 0;
    $totalTurmasComNota = 0;
    $somaFrequencias = 0;

    foreach ($turmasRaw as $turma) {
        // Média por disciplina
        $sqlMaterias = "SELECT n.materia, AVG(n.nota) as media_materia
                        FROM notas n
                        JOIN alunos a ON n.aluno_id = a.id
                        WHERE a.turma_id = :turma_id AND n.bimestre = :periodo
                        GROUP BY n.materia";
        $stmtMat = $pdo->prepare($sqlMaterias);
        $stmtMat->execute([':turma_id' => $turma['id'], ':periodo' => $periodo]);
        $materias = $stmtMat->fetchAll();

        $materiasMap = [];
        $somaNotasTurma = 0;
        foreach ($materias as $m) {
            $notaVal = round((float)$m['media_materia'], 1);
            $materiasMap[$m['materia']] = $notaVal;
            $somaNotasTurma += $notaVal;
        }

        $qtdMaterias = count($materiasMap);
        $mediaTurma = $qtdMaterias > 0 ? round($somaNotasTurma / $qtdMaterias, 1) : 0.0;

        if ($qtdMaterias > 0) {
            $somaMediasGerais += $mediaTurma;
            $totalTurmasComNota++;
        }

        $freqTurma = round((float)$turma['frequencia_media'], 1);
        $somaFrequencias += $freqTurma;

        $turmasProcessadas[] = [
            'id' => $turma['id'],
            'nome' => $turma['nome'],
            'etapa' => $turma['etapa'],
            'etapaNome' => $turma['etapa_nome'],
            'alunos' => (int)$turma['alunos'],
            'media' => $mediaTurma,
            'freq' => $freqTurma . '%',
            'obs' => $turma['observacao'] ?? 'Sem observações registadas.',
            'materias' => $materiasMap
        ];
    }

    // Ordenação para apurar KPIs
    usort($turmasProcessadas, fn($a, $b) => $b['media'] <=> $a['media']);

    $mediaGeralEscola = $totalTurmasComNota > 0 ? round($somaMediasGerais / $totalTurmasComNota, 1) : 0.0;
    $frequenciaGeralEscola = count($turmasProcessadas) > 0 ? round($somaFrequencias / count($turmasProcessadas), 1) : 0.0;

    echo json_encode([
        'sucesso' => true,
        'kpis' => [
            'mediaGeral' => $mediaGeralEscola,
            'frequenciaGeral' => $frequenciaGeralEscola . '%',
            'maiorMedia' => $turmasProcessadas[0] ?? null,
            'menorMedia' => $turmasProcessadas[count($turmasProcessadas) - 1] ?? null
        ],
        'turmas' => $turmasProcessadas
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => $e->getMessage()]);
}