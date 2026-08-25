<?php
require_once "Usuario.php";

class Aluno extends Usuario {
    private  $turma;

    public function __construct($nome,$email,$senha,$cpf,$dataNasc,$tipoUser, $instituicao, $turma) {
        parent::__construct($nome,$email,$senha,$cpf,$dataNasc,$tipoUser, $instituicao);
        $this->turma = $turma;
    }

    public function getDesempenhos(){
        return $this->desempenhos;
    }

    public function getTurma(){
        return $this->turma;
    }

}
?>