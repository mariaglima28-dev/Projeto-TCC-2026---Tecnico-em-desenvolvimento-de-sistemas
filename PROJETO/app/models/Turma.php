<?php
require_once 'Professor.php';
require_once 'Aluno.php';

class Turma {
    private $id;
    private $nome;
    private $professor;
    private array $alunos = [];
    private $aulas = [];

    public function __construct($id, $nome, $professor){
        $this->id = $id;
        $this->nome = $nome;
        $this->professor = $professor;

        $professor->adicionarTurma($this);
    }

    public function adicionarAluno(Aluno $aluno){
        $this->alunos[] = $aluno;
        $aluno->setTurma($this);
    }

    public function adicionarAula(Aula $aula){
        $this->aula = $aula;
    }

    public function getAlunos(){
        return $this->alunos;
    }

    public function getAulas(){
        return $this->aulas;
    }
}



?>