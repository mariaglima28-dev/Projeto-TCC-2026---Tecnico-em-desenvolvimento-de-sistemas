<?php
require_once 'Aluno.php';
require_once 'Atividade.php';

class Desempenho {
    private $id;
    private Aluno $aluno;
    private Atividade $atividade;
    private float $nota;
    private $dataRealizacao;

    public function __construct($id, Aluno $aluno, Atividade $atividade, float $nota, $dataREalizacao){
        $this->id = $id;
        $this->aluno = $aluno;
        $this->atividade = $atividade;
        $this->nota = $nota;
        $this->dataRealizacao = $dataRealizacao;
    }

    public function getAluno(){
        return $this->alunos;
    }

    public function getAtividade(){
        return $this->atividade;
    }

    public function getNota(){
        return $this->nota;
    }

    public function setNota(){
        return $this->nota;
    }
    
    public function getDataRealizacao(){
        return $this->dataRealizacao;
    }
}



?>