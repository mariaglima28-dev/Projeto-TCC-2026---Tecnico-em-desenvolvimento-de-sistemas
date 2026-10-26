<?php
require_once 'Usuario.php';
require_once 'Turmas.php';
require_once 'Interacao.php';

class Professor extends Usuario {
    private $disciplina;

    public function __construct($nome,$email,$senha,$cpf,$dataNasc,$tipoUser, $instituicao, $disciplina) {
        parent::__construct($nome,$email,$senha,$cpf,$dataNasc,$tipoUser, $instituicao);
        $this->disciplina = $disciplina;
    }

    public function adicionarTurma(Turma $turma){
        $this->turmas[] = $turma;
    }

    public function getDisciplina(){
        return $this->disciplina;
    }

    public function getTurma(){
        return $this->turmas;
    }
}




?>