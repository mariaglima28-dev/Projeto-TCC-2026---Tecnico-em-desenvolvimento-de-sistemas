<?php
require_once "Turma.php";
require_once "Professor.php";

class Disciplina {
    private $id;
    private $nomeMateria;
    private $turma;
    private $professor;

    public function __construct($nomeMateria, $turma, $professor){
        $this->id = rand(1, 1000);
        $this->nomeMateria = $nomeMateria;
        $this->turma = $turma;
        $this->professor = $professor;
    }

    public function Todos(){
        return [
            $this->id,
            $this->nome,
            $this->turma,
            $this->professor
        ];
    }

    public function getTodos(){
        return $this->Todos();
    }

    public function setTurma($turma){
        return $this->turma = $turma;
    }

    public function setProfessor($professor){
        return $this->professor = $professor;
    }
}


?>